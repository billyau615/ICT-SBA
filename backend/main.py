#!/usr/bin/env python3
from flask import Flask, request, jsonify
import sqlite3
import hashlib
import datetime
import os
import be_config as cfg
import be_payment as payment
import be_flights as flights
import be_documentcheckermathematics as dchecker
import pyotp
import uuid
import be_str2datetime as s2d
import time
import pyqrcode
import base64
import io
app = Flask(__name__)

DATABASE = cfg.GET_DATABASE()

def user_exists(username):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute('SELECT 1 FROM users WHERE username = ?', (username,))
    exists = cursor.fetchone() is not None
    conn.close()
    return exists

def format_time(four_digit_time):
    time_str = str(four_digit_time).zfill(4)
    return time_str[:2] + ':' + time_str[2:]

@app.route('/api/signup', methods=['POST'])
def signup():
    try:
        username = request.form.get('username')
        password = request.form.get('password')
        fname = request.form.get('fname')
        lname = request.form.get('lname')
        telcode = request.form.get('telcode')
        tel = request.form.get('tel')
        gender = request.form.get('gender')
        dob = request.form.get('dob')
        country = request.form.get('country')
        registration_date = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        if user_exists(username):
            return jsonify({'status': 1})
        mfa = "false"
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute('INSERT INTO users (username, password, registration_date, fname, lname, telcode, tel, gender, dob, country, mfa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', (username, password, registration_date, fname, lname, telcode, tel, gender, dob, country, mfa))
        conn.commit()
        conn.close()
        return jsonify({'status': 0})
    except Exception as e:
        return jsonify({'status': 3})

def check_credentials(username, password=""):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute('SELECT password FROM users WHERE username = ?', (username,))
    row = cursor.fetchone()
    conn.close()
    
    if row is None:
        return 1
    elif row[0] != password:
        return 2
    else:
        return 0

def check2fa(username):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute('SELECT mfa FROM users WHERE username = ?', (username,))
    row = cursor.fetchone()
    conn.close()
    if row[0] == "true":
        return 0
    elif row[0] == "false":
        return 1
    else:
        return 2

@app.route('/api/login', methods=['POST'])
def login():
    try:
        username = request.form.get('username')
        password = request.form.get('password')
        
        status = check_credentials(username, password)
        
        if status == 0:
            check2fa_status = check2fa(username)
            if check2fa_status == 0:
                return jsonify({'status': 9})
            else:
                return jsonify({'status': 0})
        elif status == 1 or status == 2:
            return jsonify({'status': 1})
    except Exception as e:
        return jsonify({'status': 3})

@app.route('/api/forgotpassword', methods=['POST'])
def forgotpassword():
    try:
        username = request.form.get('username')
        if user_exists(username):
            expire = datetime.datetime.now() + datetime.timedelta(minutes=20)
            resetid = str(uuid.uuid4())
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute('INSERT INTO forgotpassword (username, resetid, expire) VALUES (?, ?, ?)', (username, resetid, expire))
            conn.commit()
            conn.close()
            link = f"https://sba.billyau.net/972b1b53-d0e8-4625-b159-12617a419cb5/forgotpw.php?resetid={resetid}"
            print ("Reset password link request from: " + username)
            print (link)
            # Update link in db
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute('UPDATE temp SET url = ?, datetime = ? WHERE id = 1', (link, datetime.datetime.now()))
            conn.commit()
            conn.close()
            return jsonify({'status': 0})
        else:
            print ("Reset password link request received from non-existing user: " + username)
            return jsonify({'status': 0})
    except Exception as e:
        return jsonify({'status': 3})
        
@app.route('/api/obtaintempdata', methods=['POST'])
def obtaintempdata():
    try:
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute('SELECT datetime, url FROM temp WHERE id = 1')
        row = cursor.fetchone()
        conn.close()
        datetime = row[0]
        url = row[1]
        if row is not None:
            return jsonify({'status': 0, 'datetime': datetime, 'url': url})
        else:
            return jsonify({'status': 1})
    except Exception as e:
        return jsonify({'status': 3})

@app.route('/api/checkresetid', methods=['POST'])
def checkresetid():
    try:
        resetid = request.form.get('resetid')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute('SELECT expire FROM forgotpassword WHERE resetid = ?', (resetid,))
        row = cursor.fetchone()
        conn.close()
        if row is not None:
            expire_str = row[0]
            expire_str = expire_str.split('.')[0]
            expire = datetime.datetime.strptime(expire_str, '%Y-%m-%d %H:%M:%S')
            if datetime.datetime.now() < expire:
                return jsonify({'status': 0})
            else:
                return jsonify({'status': 1})
        else:
            return jsonify({'status': 1})
    except Exception as e:
        print(str(e))
        return jsonify({'status': 3})


def resetpassword_db(newpassword, username):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute('UPDATE users SET password = ? WHERE username = ?', (newpassword, username))
    conn.commit()
    conn.close()

@app.route('/api/resetpassword', methods=['POST'])
def resetpassword():
    try:
        password = request.form.get('password')
        resetid = request.form.get('resetid')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute('SELECT username FROM forgotpassword WHERE resetid = ?', (resetid,))
        row = cursor.fetchone()
        conn.close()
        if row is not None:
            username = row[0]
            resetpassword_db(password, username)
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute('DELETE FROM forgotpassword WHERE resetid = ?', (resetid,))
            conn.commit()
            conn.close()
            return jsonify({'status': 0})
        else:
            return jsonify({'status': 1})
    except Exception as e:
        return jsonify({'status': 3})

@app.route('/api/get_user_info', methods=['POST'])
def get_user_info():
    try:
        username = request.form.get('username')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute('SELECT fname, lname, username, telcode, tel, gender, country, dob, mfa FROM users WHERE username = ?', (username,))
        row = cursor.fetchone()
        conn.close()
        if row is not None:
            fname = row[0]
            lname = row[1]
            username = row[2]
            telcode = row[3]
            tel = row[4]
            gender = row[5]
            country = row[6]
            dob = row[7]
            mfa = row[8]
            return jsonify({'status': 0, 'fname': fname, 'lname': lname, 'username': username, 'telcode': telcode, 'tel': tel, 'gender': gender, 'country': country, 'dob': dob, 'mfa': mfa})
        else:
            return jsonify({'status': 1})
    except Exception as e:
        print(str(e))
        return jsonify({'status': 3})

@app.route('/api/change_user_info', methods=['POST'])
def change_user_info():
    try:
        username = request.form.get('username') # Old username of user
        if not user_exists(username):
            return jsonify({'status': 1})
        email = request.form.get('email') # New username of user
        if email != username and user_exists(email):
            return jsonify({'status': 2})
        fname = request.form.get('fname')
        lname = request.form.get('lname')
        telcode = request.form.get('telcode')
        tel = request.form.get('tel')
        gender = request.form.get('gender')
        country = request.form.get('country')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute('UPDATE users SET username = ?, fname = ?, lname = ?, telcode = ?, tel = ?, gender = ?, country = ? WHERE username = ?', (email, fname, lname, telcode, tel, gender, country, username))
        conn.commit()
        conn.close()
        return jsonify({'status': 0})
    except Exception as e:
        print(str(e))
        return jsonify({'status': 3})

@app.route('/api/search_flights', methods=['POST'])
def search_flights():
    try:
        src = request.form.get('departure')
        dst = request.form.get('arrival')
        triptype = request.form.get('triptype')
        searched_flight = flights.search_flights(src, dst)[0]
        if searched_flight is None:
            return jsonify({'status': 1})
        if triptype == "return":
            src, dst = dst, src
            searched_flight_return = flights.search_flights(src, dst)[0]
            if searched_flight_return is None:
                return jsonify({'status': 1})
            return jsonify({'status': 0, 'flight': searched_flight, 'flight_return': searched_flight_return})
        return jsonify({'status': 0, 'flight': searched_flight})
    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500


@app.route('/api/createorder', methods=['POST'])
def createorder():
    try:
        # Define username
        username = request.form.get('username')
        # Define flight details from POST request
        flight_number = request.form.get('flight')
        flight_number_return = request.form.get('flight_return')
        triptype = request.form.get('triptype')
        departingdate = request.form.get('departingdate')
        returningdate = request.form.get('returningdate')
        numofadult = request.form.get('numofadult')
        numofchild = request.form.get('numofchild')
        cabinclass = request.form.get('cabinclass')

        # Define expires datetime
        expire = datetime.datetime.now() + datetime.timedelta(minutes=20)

        # Obtain details of departing flight
        details = flights.flight_details(flight_number)
        det = {}
        det['etd'] = format_time(details[1])
        det['eta'] = format_time(details[2])
        det['src'] = details[3]
        det['src_long'] = flights.icao_code_convert(det['src'])
        det['srct'] = details[4]
        det['dst'] = details[5]
        det['dst_long'] = flights.icao_code_convert(det['dst'])
        det['dstt'] = details[6]
        det['economy_price'] = details[7]
        det['business_price'] = details[8]
        det['first_price'] = details[9]
        det['aircraft'] = details[10]

        # Obtain details of departing flight, if applicable
        if triptype == "return":
            details_return = flights.flight_details(flight_number_return)
            det_return = {}
            det_return['etd'] = format_time(details_return[1])
            det_return['eta'] = format_time(details_return[2])
            det_return['src'] = details_return[3]
            det_return['src_long'] = flights.icao_code_convert(det_return['src'])
            det_return['srct'] = details_return[4]
            det_return['dst'] = details_return[5]
            det_return['dst_long'] = flights.icao_code_convert(det_return['dst'])
            det_return['dstt'] = details_return[6]
            det_return['economy_price'] = details_return[7]
            det_return['business_price'] = details_return[8]
            det_return['first_price'] = details_return[9]
            det_return['aircraft'] = details_return[10]

        # Define price

        if cabinclass == "economy":
            det['price_a'] = det['economy_price']
            if triptype == "return":
                det_return['price_a'] = det_return['economy_price']
        elif cabinclass == "business":
            det['price_a'] = det['business_price']
            if triptype == "return":
                det_return['price_a'] = det_return['business_price']
        elif cabinclass == "first":
            det['price_a'] = det['first_price']
            if triptype == "return":
                det_return['price_a'] = det_return['first_price']

        # Define price for children
        det['price_c'] = round(int(det['price_a']) * 0.7)
        if triptype == "return":
            det_return['price_c'] = round(int(det_return['price_a']) * 0.7)
            
        # Generate and Define UUID 
        orderid = str(uuid.uuid4())

        # Define order status
        status = 0

        # Create order
        if triptype == "return":
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute("INSERT INTO pendingorder (orderid, username, status, triptype, cabinclass, flight_number, flight_number_return, src, src_long, dst, dst_long, departingdate, departingetd, departingeta, returningdate, returningetd, returningeta, departingprice_a, departingprice_c, returningprice_a, returningprice_c, numofadult, numofchild, expire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", (orderid, username, status, triptype, cabinclass, flight_number, flight_number_return, det['src'], det['src_long'], det['dst'], det['dst_long'], departingdate, det['etd'], det['eta'], returningdate, det_return['etd'], det_return['eta'], det['price_a'], det['price_c'], det_return['price_a'], det_return['price_c'], numofadult, numofchild, expire))
            conn.commit()
            conn.close()
            return jsonify({'status': 0, 'orderid': orderid}), 200

        elif triptype == "one-way":
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute("INSERT INTO pendingorder (orderid, username, status, triptype, cabinclass, flight_number, src, src_long, dst, dst_long, departingdate, departingetd, departingeta, departingprice_a, departingprice_c, numofadult, numofchild, expire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", (orderid, username, status, triptype, cabinclass, flight_number, det['src'], det['src_long'], det['dst'], det['dst_long'], departingdate, det['etd'], det['eta'], det['price_a'], det['price_c'], numofadult, numofchild, expire))
            conn.commit()
            conn.close()
            return jsonify({'status': 0, 'orderid': orderid}), 200
        else :
            return jsonify({'status': 1, 'message': 'Error Occured'}), 400
    except Exception as e:
        print (str(e))
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/reviewflights', methods=['POST'])
def reviewflights():
    try:
        orderid = request.form.get('orderid')
        username = request.form.get('username')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM pendingorder WHERE orderid = ?", (orderid,))
        row = cursor.fetchone()
        conn.close()

        if row is not None:
            order = {
                'expire': row[1],
                'orderid': row[2],
                'username': row[3],
                'status': row[4],
                'triptype': row[5],
                'cabinclass': row[6],
                'flight_number': row[7],
                'src': row[9],
                'src_long': row[10],
                'dst': row[11],
                'dst_long': row[12],
                'departingdate': row[13],
                'departingetd': row[14],
                'departingeta': row[15],
                'departingprice_a': row[19],
                'departingprice_c': row[20],
                'numofadult': row[23],
                'numofchild': row[24]
            }
            if row[5] == "return":
                order['flight_number_return'] = row[8]
                order['returningdate'] = row[16]
                order['returningetd'] = row[17]
                order['returningeta'] = row[18]
                order['returningprice_a'] = row[21]
                order['returningprice_c'] = row[22]

            if username != order['username']:
                return jsonify({'status': 1, 'message': 'Error Occured'}), 400

            if order['status'] != 0:
                return jsonify({'status': 1, 'message': 'Error Occured'}), 400
            
            expire_str = order['expire']
            expire_str = expire_str.split('.')[0]
            expire = datetime.datetime.strptime(expire_str, '%Y-%m-%d %H:%M:%S')
            if datetime.datetime.now() < expire:
                return jsonify(order), 200
        else:
            return jsonify({'status': 1, 'message': 'Error Occured'}), 400
    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/submitpassinfo', methods=['POST'])
def submitpassinfo():
    try:
        orderid = request.form.get('orderid')
        username = request.form.get('username')
        numofadult = int(request.form.get('numofadult'))
        numofchild = int(request.form.get('numofchild'))

        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("SELECT username, expire, status FROM pendingorder WHERE orderid = ?", (orderid,))
        row = cursor.fetchone()
        conn.close()

        if row is not None:
            if username != row[0]:
                return jsonify({'status': 1, 'message': 'Error Occured'}), 400
            
            expire_str = row[1].split('.')[0] 
            expire = datetime.datetime.strptime(expire_str, '%Y-%m-%d %H:%M:%S')
            if datetime.datetime.now() > expire:
                return jsonify({'status': 1, 'message': 'Error Occured'}), 400

            if row[2] != 0:
                return jsonify({'status': 1, 'message': 'Error Occured'}), 400

            passenger_data = {}

            for i in range(1, numofadult + 1):
                passenger_data[f'a{i}_fname'] = request.form.get(f'a{i}_fname')
                passenger_data[f'a{i}_lname'] = request.form.get(f'a{i}_lname')
                passenger_data[f'a{i}_dob'] = request.form.get(f'a{i}_dob')
                passenger_data[f'a{i}_telcode'] = request.form.get(f'a{i}_telcode')
                passenger_data[f'a{i}_tel'] = request.form.get(f'a{i}_tel')
                passenger_data[f'a{i}_email'] = request.form.get(f'a{i}_email')

            for i in range(1, numofchild + 1):
                passenger_data[f'c{i}_fname'] = request.form.get(f'c{i}_fname')
                passenger_data[f'c{i}_lname'] = request.form.get(f'c{i}_lname')
                passenger_data[f'c{i}_dob'] = request.form.get(f'c{i}_dob')
                passenger_data[f'c{i}_telcode'] = request.form.get(f'c{i}_telcode')
                passenger_data[f'c{i}_tel'] = request.form.get(f'c{i}_tel')
                passenger_data[f'c{i}_email'] = request.form.get(f'c{i}_email')

            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()

            update_query = "UPDATE pendingorder SET "
            update_fields = []
            update_values = []

            for i in range(1, numofadult + 1):
                update_fields.append(f'a{i}_fname = ?, a{i}_lname = ?, a{i}_dob = ?, a{i}_telcode = ?, a{i}_tel = ?, a{i}_email = ?')
                update_values.extend([
                    passenger_data[f'a{i}_fname'], passenger_data[f'a{i}_lname'], passenger_data[f'a{i}_dob'], 
                    passenger_data[f'a{i}_telcode'], passenger_data[f'a{i}_tel'], passenger_data[f'a{i}_email']
                ])

            for i in range(1, numofchild + 1):
                update_fields.append(f'c{i}_fname = ?, c{i}_lname = ?, c{i}_dob = ?, c{i}_telcode = ?, c{i}_tel = ?, c{i}_email = ?')
                update_values.extend([
                    passenger_data[f'c{i}_fname'], passenger_data[f'c{i}_lname'], passenger_data[f'c{i}_dob'], 
                    passenger_data[f'c{i}_telcode'], passenger_data[f'c{i}_tel'], passenger_data[f'c{i}_email']
                ])

            update_query += ", ".join(update_fields) + " WHERE orderid = ?"
            update_values.append(orderid)

            cursor.execute(update_query, update_values)
            conn.commit()
            conn.close()

            discount_a = 0
            discount_c = 0
            #Find if an adult's first and last name include letter "S" "K" "Y" (Not case sensitive), if yes, add discount_a by 1 per person
            for i in range(1, numofadult + 1):
                if "s" in passenger_data[f'a{i}_fname'].lower() or "s" in passenger_data[f'a{i}_lname'].lower() or "k" in passenger_data[f'a{i}_fname'].lower() or "k" in passenger_data[f'a{i}_lname'].lower() or "y" in passenger_data[f'a{i}_fname'].lower() or "y" in passenger_data[f'a{i}_lname'].lower():
                    discount_a += 1
            #Find if an child's first and last name include letter "S" "K" "Y" (Not case sensitive), if yes, add discount_c by 1 per person
            for i in range(1, numofchild + 1):
                if "s" in passenger_data[f'c{i}_fname'].lower() or "s" in passenger_data[f'c{i}_lname'].lower() or "k" in passenger_data[f'c{i}_fname'].lower() or "k" in passenger_data[f'c{i}_lname'].lower() or "y" in passenger_data[f'c{i}_fname'].lower() or "y" in passenger_data[f'c{i}_lname'].lower():
                    discount_c += 1

            return jsonify({'status': 0, 'discount_a': discount_a, 'discount_c': discount_c}), 200
        else:
            return jsonify({'status': 1, 'message': 'Error Occured'}), 400

    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/submitpayment', methods=['POST'])
def submitpayment():
    try:
        orderid = request.form.get('orderid')
        username = request.form.get('username')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()

        cursor.execute("""
            SELECT username, expire, status, numofadult, numofchild, triptype, flight_number, flight_number_return, departingdate, returningdate 
            FROM pendingorder 
            WHERE orderid = ?
        """, (orderid,))
        row = cursor.fetchone()
        conn.close()

        numofadult = row[3]
        numofchild = row[4]
        triptype = row[5]
        flight_number = row[6]
        flight_number_return = row[7] if triptype == "return" else None
        departingdate = row[8]
        returningdate = row[9] if triptype == "return" else None

        if row is not None:
            if username != row[0]:
                return jsonify({'status': 2, 'message': 'Error Occured'}), 450
            
            expire_str = row[1].split('.')[0]
            expire = datetime.datetime.strptime(expire_str, '%Y-%m-%d %H:%M:%S')
            if datetime.datetime.now() > expire:
                return jsonify({'status': 2, 'message': 'Error Occured'}), 450

            if row[2] != 0:
                return jsonify({'status': 2, 'message': 'Error Occured'}), 450
            
            if request.form.get('payment-method') == "creditcard":
                card = request.form.get('cardnumber')
                print (card)
                print (payment.payment(card))
                if not payment.payment(card):
                    return jsonify({'status': 1, 'card': 'false'}), 201

            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()

            adults = []
            for i in range(1, numofadult + 1):
                cursor.execute(f"SELECT a{i}_fname, a{i}_lname FROM pendingorder WHERE orderid = ?", (orderid,))
                adult = cursor.fetchone()
                adults.append(f"{adult[0]} {adult[1]}")

            children = []
            for i in range(1, numofchild + 1):
                cursor.execute(f"SELECT c{i}_fname, c{i}_lname FROM pendingorder WHERE orderid = ?", (orderid,))
                child = cursor.fetchone()
                children.append(f"{child[0]} {child[1]}")

            a_values = adults + [''] * (6 - len(adults))  
            c_values = children + [''] * (4 - len(children))

            cursor.execute("""
                INSERT INTO bookedflights (username, orderid, date, flight_number, a1, a2, a3, a4, a5, a6, c1, c2, c3, c4)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """, (username, orderid, departingdate, flight_number, *a_values, *c_values))

            departing_flight_id = cursor.lastrowid

            if triptype == "return":
                cursor.execute("""
                    INSERT INTO bookedflights (username, orderid, date, flight_number, a1, a2, a3, a4, a5, a6, c1, c2, c3, c4)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                """, (username, orderid, returningdate, flight_number_return, *a_values, *c_values))

            cursor.execute("UPDATE pendingorder SET status = 1 WHERE orderid = ?", (orderid,))
            conn.commit()
            conn.close()

            return jsonify({
                'status': 0, 
                'message': 'Success',
                'flight_id': departing_flight_id
            }), 200

    except Exception as e:
        print(str(e))
        return jsonify({'status': 3, 'message': f'Error Occured: {str(e)}'}), 500

@app.route('/api/getupcomingtrips', methods=['POST'])
def getupcomingtrips():
    try:
        username = request.form.get('username')
        if not user_exists(username):
            return jsonify({'status': 1, 'message': 'Error Occured'}), 400
        
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM bookedflights WHERE username = ?", (username,))
        rows = cursor.fetchall()
        conn.close()

        trips = []
        for row in rows:
            passenger_names = [
                row[5], row[6], row[7], row[8], row[9], row[10], 
                row[11], row[12], row[13], row[14] 
            ]

            passenger_names_combined = ", ".join(name for name in passenger_names if name)

            trip = {
                'id': row[0],
                'orderid': row[2],
                'date': row[3],
                'flight_number': row[4],
                'passenger_names': passenger_names_combined
            }
            trips.append(trip)

        return jsonify({'status': 0, 'trips': trips}), 200
    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/getflightdetails', methods=['POST'])
def getflightdetails():
    try:
        details = flights.flight_details(request.form.get('flight_number'))
        if details is None:
            return jsonify({'status': 1, 'message': 'Error Occured'}), 400
        det = {}
        det['etd'] = format_time(details[1])
        det['eta'] = format_time(details[2])
        det['src'] = details[3]
        det['src_long'] = flights.icao_code_convert(det['src'])
        det['srct'] = details[4]
        det['dst'] = details[5]
        det['dst_long'] = flights.icao_code_convert(det['dst'])
        det['dstt'] = details[6]
        det['economy_price'] = details[7]
        det['business_price'] = details[8]
        det['first_price'] = details[9]
        det['aircraft'] = details[10]
        print (det)
        return jsonify({'status': 0, 'src_long': det['src_long'], 'dst_long': det['dst_long'], 'etd': det['etd'], 'eta': det['eta'], 'aircraft': det['aircraft']}), 200

    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/create2fa', methods=['POST'])
def create2fa():
    try:
        username = request.form.get('username')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("SELECT mfa FROM users WHERE username = ?", (username,))
        rows = cursor.fetchall()
        conn.close()
        if rows[0] == "true":
            return jsonify({'status': 2, 'message': 'Error Occured'}), 400
        else:
            secret = pyotp.random_base32()
            qr_url = pyotp.totp.TOTP(secret).provisioning_uri(name=username, issuer_name='Sky Airlines')
            qr_code = pyqrcode.create(qr_url)
            buffer = io.BytesIO()
            qr_code.png(buffer, scale=5)
            qr_base64 = base64.b64encode(buffer.getvalue()).decode('utf-8')
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute("UPDATE users SET mfa_secret = ? WHERE username = ?", (secret, username))
            conn.commit()
            conn.close()
            return jsonify({'status': 0, 'message': qr_base64}), 200
    except Exception as e:
        print (str(e))
        print (request.form.get('username'))
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/setup2fa', methods=['POST'])
def setup2fa():
    try:
        username = request.form.get('username')
        otp = request.form.get('otp')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("SELECT mfa FROM users WHERE username = ?", (username,))
        rows = cursor.fetchall()
        conn.close()
        if rows[0][0] == "true":
            return jsonify({'status': 2, 'message': 'Error Occured'}), 400
        else:
            conn = sqlite3.connect(DATABASE)
            cursor = conn.cursor()
            cursor.execute("SELECT mfa_secret FROM users WHERE username = ?", (username,))
            rows = cursor.fetchall()
            conn.close()
            totp = pyotp.TOTP(rows[0][0])
            if totp.verify(otp):
                conn = sqlite3.connect(DATABASE)
                cursor = conn.cursor()
                cursor.execute("UPDATE users SET mfa = ? WHERE username = ?", ("true", username))
                conn.commit()
                conn.close()
                return jsonify({'status': 0, 'message': 'Success'}), 200
            else:
                return jsonify({'status': 1, 'message': 'Incorrect'}), 200

    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/verify2fa', methods=['POST'])
def verify2fa():
    try:
        username = request.form.get('username')
        otp = request.form.get('otp')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("SELECT mfa_secret FROM users WHERE username = ?", (username,))
        rows = cursor.fetchall()
        conn.close()
        totp = pyotp.TOTP(rows[0][0])
        if totp.verify(otp):
            return jsonify({'status': 0, 'message': 'Success'}), 200
        else:
            return jsonify({'status': 1, 'message': 'Incorrect'}), 200

    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500

@app.route('/api/disable2fa', methods=['POST'])
def disable2fa():
    try:
        username = request.form.get('username')
        conn = sqlite3.connect(DATABASE)
        cursor = conn.cursor()
        cursor.execute("UPDATE users SET mfa = ? WHERE username = ?", ("false", username))
        conn.commit()
        conn.close()
        return jsonify({'status': 0, 'message': 'Success'}), 200

    except Exception as e:
        return jsonify({'status': 3, 'message': str(e)}), 500



if __name__ == '__main__':
    app.run(debug=True)


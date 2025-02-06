#!/usr/bin/env python3
import sqlite3
import datetime
import be_config as cfg

DATABASE = cfg.GET_DATABASE()

def flight_details(flight_number):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute("SELECT flight_number, etd, eta, src, srct, dst, dstt, economy_price, business_price, first_price, aircraft FROM flights WHERE flight_number = ?", (flight_number,))
    det = cursor.fetchone()
    return det

def check_booked_flights(username):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute("SELECT flight_number, seatclass FROM booked_flights WHERE username = ?", (username, ))
    all_booked_flights = cursor.fetchall()
    return all_booked_flights

def search_flights(src, dst):
    conn = sqlite3.connect(DATABASE)
    cursor = conn.cursor()
    cursor.execute("SELECT flight_number FROM flights WHERE src = ? AND dst = ?", (src, dst))
    searched_flight = cursor.fetchone() # Assume there's only one flight per route per day for now.
    return searched_flight

def icao_code_convert(icao_code):
    airport_list = [
        ("HKG", "Hong Kong SAR, PRC (HKG)"),
        ("PEK", "Beijing, PRC (PEK)"),
        ("SHA", "Shanghai, PRC (SHA)"),
        ("SZX", "Shenzhen, PRC (SZX)"),
        ("TPE", "Taipei, China (TPE)"),
        ("NRT", "Tokyo, Japan (NRT)"),
        ("ICN", "Seoul, South Korea (ICN)"),
        ("SIN", "Singapore, Singapore (SIN)"),
        ("LAX", "Los Angeles, USA (LAX)"),
        ("SFO", "San Francisco, USA (SFO)"),
        ("LHR", "London, United Kingdom (LHR)"),
        ("SYD", "Sydney, Australia (SYD)"),
        ("CDG", "Paris, France (CDG)"),
        ("FRA", "Frankfurt, Germany (FRA)")
        ]
    for airport in airport_list:
        if icao_code == airport[0]:
            return airport[1]
    return "Unknown"


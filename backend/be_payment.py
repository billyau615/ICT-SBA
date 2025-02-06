import sqlite3
import be_documentcheckermathematics as dchecker

def payment(cardnumber):
    if dchecker.luhn_check(cardnumber):
        return True
    else:
        return False
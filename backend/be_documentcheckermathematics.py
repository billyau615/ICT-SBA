#!/usr/bin/env python3

def luhn_check(card_number: str) -> bool:
    card_number = ''.join(filter(str.isdigit, card_number))
    digits = [int(d) for d in card_number[::-1]]
    for i in range(1, len(digits), 2):
        digits[i] *= 2
        if digits[i] > 9:
            digits[i] -= 9
    total_sum = sum(digits)
    
    return total_sum % 10 == 0

def passport_check(passport_number: str) -> bool:
    if len(passport_number) > 9 or len(passport_number) < 7:
        return False
    else:
        return True

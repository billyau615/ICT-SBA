from datetime import datetime

def str2datetime(expire_str):
    expire = datetime.strptime(expire_str, "%Y-%m-%d %H:%M:%S.%f")
    return expire
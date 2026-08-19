#!/usr/bin/env python3
"""
Helper for AGASOBANUYE TV portal — generates a properly salted SHA-256
password entry and prints (or appends) it to portal/assets/json/login-info.json.

This matches exactly how portal/assets/js/auth.js verifies logins:
    hash = sha256(salt + plaintext_password)

Usage:
    python3 tools/add-user.py
    python3 tools/add-user.py --append   # writes straight into login-info.json
"""
import argparse
import datetime
import getpass
import hashlib
import json
import secrets
from pathlib import Path

JSON_PATH = Path(__file__).resolve().parent.parent / "portal" / "assets" / "json" / "login-info.json"


def make_user(username, email, telnumber, password, role):
    salt = secrets.token_hex(16)
    password_hash = hashlib.sha256((salt + password).encode("utf-8")).hexdigest()
    return {
        "username": username,
        "email": email,
        "telnumber": telnumber,
        "salt": salt,
        "passwordHash": password_hash,
        "role": role,
        "createdat": datetime.datetime.now(datetime.timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
    }


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--append", action="store_true", help="Append directly to login-info.json")
    args = parser.parse_args()

    username = input("Username: ").strip()
    email = input("Email: ").strip()
    telnumber = input("Phone number: ").strip()
    role = (input("Role [admin/other] (default: other): ").strip() or "other")
    password = getpass.getpass("Password: ")
    confirm = getpass.getpass("Confirm password: ")
    if password != confirm:
        raise SystemExit("Passwords did not match. Aborted.")

    user = make_user(username, email, telnumber, password, role)

    if args.append:
        data = json.loads(JSON_PATH.read_text())
        data.setdefault("users", []).append(user)
        JSON_PATH.write_text(json.dumps(data, indent=2) + "\n")
        print(f"\nAppended '{username}' to {JSON_PATH}")
    else:
        print("\nAdd this object to the \"users\" array in login-info.json:\n")
        print(json.dumps(user, indent=2))


if __name__ == "__main__":
    main()

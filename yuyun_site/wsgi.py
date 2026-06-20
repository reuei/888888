# -*- coding: utf-8 -*-
# 通用 WSGI 入口（适用于 mod_wsgi / uWSGI / Gunicorn）
import os
import sys

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
if BASE_DIR not in sys.path:
    sys.path.insert(0, BASE_DIR)

from app import app as application

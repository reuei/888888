# -*- coding: utf-8 -*-
# 用于 cPanel / LiteSpeed / Apache + Phusion Passenger 的 WSGI 入口
import os
import sys

# 将项目目录加入 Python 路径
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
if BASE_DIR not in sys.path:
    sys.path.insert(0, BASE_DIR)

from app import app as application

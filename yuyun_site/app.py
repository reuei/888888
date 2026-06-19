import os
import sqlite3
from datetime import datetime
from functools import wraps
from flask import (Flask, render_template, request, redirect, url_for,
                   session, flash, g, jsonify, send_from_directory)
from werkzeug.utils import secure_filename

BASE_DIR = os.path.abspath(os.path.dirname(__file__))
DB_PATH = os.path.join(BASE_DIR, "yuyun.db")
UPLOAD_DIR = os.path.join(BASE_DIR, "static", "uploads")
os.makedirs(UPLOAD_DIR, exist_ok=True)

app = Flask(__name__)
app.config["SECRET_KEY"] = "yuyun-keji-secret-key-2026-06-19"
app.config["UPLOAD_FOLDER"] = UPLOAD_DIR
app.config["MAX_CONTENT_LENGTH"] = 16 * 1024 * 1024
ALLOWED_EXT = {"png", "jpg", "jpeg", "gif", "svg", "webp", "bmp", "ico"}


# ---------- DB ----------
def get_db():
    db = getattr(g, "_database", None)
    if db is None:
        db = g._database = sqlite3.connect(DB_PATH)
        db.row_factory = sqlite3.Row
    return db


@app.teardown_appcontext
def close_db(exception):
    db = getattr(g, "_database", None)
    if db is not None:
        db.close()


def query(sql, args=(), one=False):
    cur = get_db().execute(sql, args)
    rv = cur.fetchall()
    cur.close()
    return (rv[0] if rv else None) if one else rv


def execute(sql, args=()):
    db = get_db()
    cur = db.execute(sql, args)
    db.commit()
    last_id = cur.lastrowid
    cur.close()
    return last_id


# ---------- Init ----------
def init_db():
    if os.path.exists(DB_PATH):
        return False
    execute("""CREATE TABLE settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )""")
    execute("""CREATE TABLE sliders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        subtitle TEXT,
        image TEXT,
        link TEXT,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE partners (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        logo TEXT,
        url TEXT,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        summary TEXT,
        icon TEXT,
        image TEXT,
        content TEXT,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE offices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        region TEXT,
        lat REAL,
        lng REAL,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE certifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        image TEXT,
        description TEXT,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE testimonials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        title TEXT,
        avatar TEXT,
        content TEXT,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE friendlinks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        url TEXT NOT NULL,
        sort INTEGER DEFAULT 0
    )""")
    execute("""CREATE TABLE news (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        summary TEXT,
        image TEXT,
        content TEXT,
        created_at TEXT DEFAULT (datetime('now','localtime'))
    )""")
    execute("""CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )""")

    # defaults
    defaults = {
        "site_name": "语云科技",
        "site_name_en": "YuYun Technology",
        "site_logo": "/static/images/logo-white.svg",
        "site_logo_dark": "/static/images/logo.svg",
        "site_favicon": "/static/images/favicon.svg",
        "company_name": "语云科技美国有限公司",
        "company_cn": "语云科技（北京）有限公司",
        "company_address": "中国北京市海淀区中关村大街1号 语云科技大厦",
        "company_address_en": "1 Zhongguancun Street, Haidian District, Beijing, China",
        "sales_phone": "400-800-8451",
        "sales_phone2": "400-800-8541",
        "service_phone": "010-8888-6666",
        "service_email": "service@yuyun-keji.com",
        "company_intro": "语云科技是一家致力于云计算、人工智能与数字化基础设施建设的全球性科技企业。我们为政府、金融、企业与开发者提供高可用、安全、高性能的云服务与数字化解决方案。",
        "company_intro_long": "语云科技在美国、中国、俄罗斯、韩国、新加坡、澳大利亚、中东及欧洲设有分公司与数据中心，致力于为全球客户提供稳定可靠的云计算服务。我们与众多国际知名厂商携手共进，构建覆盖全球的云网络。",
        "icp": "京ICP备2024000000号-1",
        "icp_url": "https://beian.miit.gov.cn/",
        "police": "京公网安备 11010802000000号",
        "police_url": "http://www.beian.gov.cn/",
        "zengxin": "B1-20240000",
        "zengxin_text": "增值电信业务经营许可证",
        "copyright": "© 2018-2026 语云科技美国有限公司 版权所有",
        "footer_note": "语云科技®等是我们（语云科技美国有限公司）在中国的注册授权。",
        "international_url": "https://cloud.loveym.cloud",
        "wechat_qr": "/static/images/wechat.svg",
        "qq_group": "888666888",
        "admin_user": "admin",
        "admin_pass": "admin123",
        "banner_title": "全球云服务 · 让连接无处不在",
        "banner_subtitle": "语云科技 - 值得信赖的云与数字化合作伙伴",
    }
    for k, v in defaults.items():
        execute("INSERT INTO settings (key, value) VALUES (?, ?)", (k, v))
    execute("INSERT INTO users (username, password) VALUES (?, ?)",
            ("admin", "admin123"))

    # default sliders
    sliders = [
        ("云服务器 ECS", "弹性稳定，全球部署，秒级开通",
         "/static/images/slider1.svg", "/products", 1),
        ("云数据库 RDS", "高可用、可弹性扩展的数据库服务",
         "/static/images/slider2.svg", "/products", 2),
        ("CDN 加速", "全球节点覆盖，毫秒级响应",
         "/static/images/slider3.svg", "/products", 3),
        ("AI 智能平台", "大模型训练推理一站式平台",
         "/static/images/slider4.svg", "/products", 4),
    ]
    for row in sliders:
        execute("INSERT INTO sliders (title, subtitle, image, link, sort) VALUES (?,?,?,?,?)", row)

    # default partners
    partners = [
        ("腾讯云", "/static/images/partner-tencent.svg", "https://cloud.tencent.com/", 1),
        ("阿里云", "/static/images/partner-aliyun.svg", "https://www.aliyun.com/", 2),
        ("华为云", "/static/images/partner-huawei.svg", "https://www.huaweicloud.com/", 3),
        ("Cloudflare", "/static/images/partner-cloudflare.svg", "https://www.cloudflare.com/", 4),
        ("AWS", "/static/images/partner-aws.svg", "https://aws.amazon.com/", 5),
        ("Google Cloud", "/static/images/partner-google.svg", "https://cloud.google.com/", 6),
        ("Microsoft Azure", "/static/images/partner-azure.svg", "https://azure.microsoft.com/", 7),
        ("魔方财务", "/static/images/partner-mofang.svg", "https://www.mofangyun.com/", 8),
    ]
    for row in partners:
        execute("INSERT INTO partners (name, logo, url, sort) VALUES (?,?,?,?)", row)

    # default products
    products = [
        ("云服务器 ECS", "弹性计算资源，按需付费，全球多个数据中心可用",
         "fa-solid fa-server", "/static/images/product-ecs.svg",
         "提供Intel/AMD高性能实例，支持Windows、Linux多种操作系统，内置安全防护与自动扩展。", 1),
        ("云数据库 RDS", "MySQL / PostgreSQL / SQL Server 全托管",
         "fa-solid fa-database", "/static/images/product-rds.svg",
         "高可用主从架构、自动备份、一键恢复，企业级数据库稳定支撑。", 2),
        ("对象存储 OSS", "海量安全低成本的云端对象存储",
         "fa-solid fa-cloud-arrow-up", "/static/images/product-oss.svg",
         "EB级存储能力，99.9999999999%持久性，全生命周期管理。", 3),
        ("内容分发 CDN", "全球 2800+ 节点，智能调度加速",
         "fa-solid fa-bolt", "/static/images/product-cdn.svg",
         "动静分离，图片智能压缩，HTTPS 全站加速，HTTPS/2、QUIC 支持。", 4),
        ("DDoS 高防", "T 级防护，智能清洗，业务持续可用",
         "fa-solid fa-shield-halved", "/static/images/product-ddos.svg",
         "覆盖网络层、传输层、应用层全栈防护，秒级攻击响应。", 5),
        ("AI 大模型平台", "一键训练、部署、调用企业大模型",
         "fa-solid fa-robot", "/static/images/product-ai.svg",
         "提供GPU算力、模型微调、向量检索与RAG应用构建能力。", 6),
    ]
    for row in products:
        execute("INSERT INTO products (title, summary, icon, image, content, sort) VALUES (?,?,?,?,?,?)", row)

    # default offices (lat/lng approx)
    offices = [
        ("北京总部", "中国", 39.9042, 116.4074, 1),
        ("青岛", "中国", 36.0671, 120.3826, 2),
        ("莫斯科", "俄罗斯", 55.7558, 37.6173, 3),
        ("圣彼得堡", "俄罗斯", 59.9311, 30.3609, 4),
        ("首尔", "韩国", 37.5665, 126.9780, 5),
        ("新加坡", "东南亚", 1.3521, 103.8198, 6),
        ("悉尼", "澳大利亚", -33.8688, 151.2093, 7),
        ("纽约", "美国", 40.7128, -74.0060, 8),
        ("华盛顿", "美国", 38.9072, -77.0369, 9),
        ("旧金山", "美国", 37.7749, -122.4194, 10),
        ("伦敦", "欧洲", 51.5074, -0.1278, 11),
        ("法兰克福", "欧洲", 50.1109, 8.6821, 12),
        ("迪拜", "中东", 25.2048, 55.2708, 13),
    ]
    for row in offices:
        execute("INSERT INTO offices (name, region, lat, lng, sort) VALUES (?,?,?,?,?)", row)

    # default certifications
    certs = [
        ("营业执照", "/static/images/cert-license.svg", "企业法人营业执照（统一社会信用代码）", 1),
        ("增值电信业务经营许可证", "/static/images/cert-icp.svg", "B1-20240000 电子增值服务产业证", 2),
        ("高新技术企业证书", "/static/images/cert-hightech.svg", "国家高新技术企业认证", 3),
        ("ISO 27001", "/static/images/cert-iso.svg", "信息安全管理体系认证", 4),
    ]
    for row in certs:
        execute("INSERT INTO certifications (title, image, description, sort) VALUES (?,?,?,?)", row)

    # default testimonials
    tms = [
        ("王先生", "某金融集团 CIO", "", "语云科技的云服务稳定且响应迅速，为我们金融核心系统提供了坚实保障。", 1),
        ("李女士", "某电商平台 CTO", "", "CDN 与 DDoS 高防服务非常专业，大促期间流量平稳，故障为零。", 2),
        ("Dr. Smith", "美国某 AI 公司创始人", "", "GPU 集群调度与大模型训练平台非常易用，极大提升团队研发效率。", 3),
    ]
    for row in tms:
        execute("INSERT INTO testimonials (name, title, avatar, content, sort) VALUES (?,?,?,?,?)", row)

    # default friendlinks
    fls = [
        ("腾讯云", "https://cloud.tencent.com/", 1),
        ("阿里云", "https://www.aliyun.com/", 2),
        ("华为云", "https://www.huaweicloud.com/", 3),
        ("Cloudflare", "https://www.cloudflare.com/", 4),
        ("魔方财务", "https://www.mofangyun.com/", 5),
        ("工信部备案", "https://beian.miit.gov.cn/", 6),
    ]
    for row in fls:
        execute("INSERT INTO friendlinks (name, url, sort) VALUES (?,?,?)", row)

    return True


# ---------- Helpers ----------
def setting(key, default=""):
    row = query("SELECT value FROM settings WHERE key=?", (key,), one=True)
    return row["value"] if row else default


def all_settings():
    rows = query("SELECT key, value FROM settings")
    return {r["key"]: r["value"] for r in rows}


def allowed_file(filename):
    return "." in filename and filename.rsplit(".", 1)[1].lower() in ALLOWED_EXT


def save_upload(file_storage):
    if not file_storage or not file_storage.filename:
        return None
    if not allowed_file(file_storage.filename):
        return None
    ext = file_storage.filename.rsplit(".", 1)[1].lower()
    fname = datetime.now().strftime("%Y%m%d%H%M%S%f") + "." + ext
    fpath = os.path.join(app.config["UPLOAD_FOLDER"], fname)
    file_storage.save(fpath)
    return "/static/uploads/" + fname


# ---------- Auth ----------
def login_required(f):
    @wraps(f)
    def wrapper(*args, **kwargs):
        if not session.get("admin"):
            return redirect(url_for("admin_login", next=request.path))
        return f(*args, **kwargs)
    return wrapper


# Ensure DB is initialized before first request
@app.before_request
def ensure_db():
    if not getattr(app, "_db_initialized", False):
        if not os.path.exists(DB_PATH):
            init_db()
        else:
            try:
                query("SELECT 1 FROM settings LIMIT 1")
            except Exception:
                init_db()
        app._db_initialized = True


@app.context_processor
def inject_globals():
    try:
        s = all_settings()
        partners_sorted = query("SELECT * FROM partners ORDER BY sort ASC, id ASC")
        friendlinks = query("SELECT * FROM friendlinks ORDER BY sort ASC, id ASC")
    except Exception:
        s = {}
        partners_sorted = []
        friendlinks = []
    return {
        "s": s,
        "partners": partners_sorted,
        "friendlinks": friendlinks,
        "now_year": datetime.now().year,
    }


# ---------- Public Routes ----------
@app.route("/")
def index():
    sliders = query("SELECT * FROM sliders ORDER BY sort ASC, id ASC")
    products = query("SELECT * FROM products ORDER BY sort ASC, id ASC")
    offices = query("SELECT * FROM offices ORDER BY sort ASC, id ASC")
    certs = query("SELECT * FROM certifications ORDER BY sort ASC, id ASC")
    tms = query("SELECT * FROM testimonials ORDER BY sort ASC, id ASC")
    return render_template("index.html",
                           sliders=sliders, products=products,
                           offices=offices, certs=certs, testimonials=tms,
                           page="home")


@app.route("/about")
def about():
    return render_template("about.html", page="about")


@app.route("/company")
def company():
    return render_template("company.html", page="company")


@app.route("/products")
def products():
    items = query("SELECT * FROM products ORDER BY sort ASC, id ASC")
    return render_template("products.html", products=items, page="products")


@app.route("/product/<int:pid>")
def product_detail(pid):
    item = query("SELECT * FROM products WHERE id=?", (pid,), one=True)
    if not item:
        return redirect(url_for("products"))
    return render_template("product_detail.html", product=item, page="products")


@app.route("/contact", methods=["GET", "POST"])
def contact():
    if request.method == "POST":
        flash("感谢您的留言，我们的顾问将尽快与您联系！", "success")
        return redirect(url_for("contact"))
    return render_template("contact.html", page="contact")


@app.route("/partners")
def partners_page():
    items = query("SELECT * FROM partners ORDER BY sort ASC, id ASC")
    return render_template("partners.html", partners=items, page="partners")


@app.route("/international")
def international():
    return render_template("international.html", page="international")


@app.route("/news")
def news():
    items = query("SELECT * FROM news ORDER BY created_at DESC")
    return render_template("news.html", news=items, page="news")


@app.route("/news/<int:nid>")
def news_detail(nid):
    item = query("SELECT * FROM news WHERE id=?", (nid,), one=True)
    if not item:
        return redirect(url_for("news"))
    return render_template("news_detail.html", item=item, page="news")


@app.route("/api/offices")
def api_offices():
    items = query("SELECT * FROM offices ORDER BY sort ASC, id ASC")
    return jsonify([dict(r) for r in items])


# ---------- Admin ----------
@app.route("/admin/login", methods=["GET", "POST"])
def admin_login():
    if request.method == "POST":
        u = request.form.get("username", "").strip()
        p = request.form.get("password", "").strip()
        row = query("SELECT * FROM users WHERE username=? AND password=?", (u, p), one=True)
        if row:
            session["admin"] = u
            flash("登录成功", "success")
            return redirect(request.args.get("next") or url_for("admin_dashboard"))
        flash("用户名或密码错误", "error")
    return render_template("admin/login.html")


@app.route("/admin/logout")
def admin_logout():
    session.pop("admin", None)
    flash("已退出登录", "success")
    return redirect(url_for("admin_login"))


@app.route("/admin")
@login_required
def admin_dashboard():
    counts = {
        "sliders": query("SELECT COUNT(*) AS c FROM sliders", one=True)["c"],
        "partners": query("SELECT COUNT(*) AS c FROM partners", one=True)["c"],
        "products": query("SELECT COUNT(*) AS c FROM products", one=True)["c"],
        "offices": query("SELECT COUNT(*) AS c FROM offices", one=True)["c"],
        "certs": query("SELECT COUNT(*) AS c FROM certifications", one=True)["c"],
        "testimonials": query("SELECT COUNT(*) AS c FROM testimonials", one=True)["c"],
        "friendlinks": query("SELECT COUNT(*) AS c FROM friendlinks", one=True)["c"],
    }
    return render_template("admin/dashboard.html", counts=counts)


@app.route("/admin/settings", methods=["GET", "POST"])
@login_required
def admin_settings():
    if request.method == "POST":
        for key, value in request.form.items():
            if key in ("submit",):
                continue
            execute("INSERT INTO settings (key, value) VALUES (?, ?) "
                    "ON CONFLICT(key) DO UPDATE SET value=excluded.value",
                    (key, value.strip() if isinstance(value, str) else value))
        # file uploads
        for fkey in ("site_logo", "site_logo_dark", "site_favicon", "wechat_qr"):
            f = request.files.get(fkey)
            if f and f.filename:
                url = save_upload(f)
                if url:
                    execute("INSERT INTO settings (key, value) VALUES (?, ?) "
                            "ON CONFLICT(key) DO UPDATE SET value=excluded.value",
                            (fkey, url))
        flash("设置已保存", "success")
        return redirect(url_for("admin_settings"))
    return render_template("admin/settings.html")


def generic_crud(table, columns, list_url, form_template,
                 upload_cols=None, order="sort ASC, id ASC"):
    upload_cols = upload_cols or []
    if request.method == "POST":
        action = request.form.get("action", "save")
        rid = request.form.get("id")
        data = {c: request.form.get(c, "") for c in columns}
        for c in upload_cols:
            f = request.files.get(c)
            if f and f.filename:
                url = save_upload(f)
                if url:
                    data[c] = url
        if action == "delete" and rid:
            execute(f"DELETE FROM {table} WHERE id=?", (int(rid),))
            flash("已删除", "success")
            return redirect(url_for(list_url))
        if rid:
            set_clause = ", ".join(f"{c}=?" for c in data.keys())
            execute(f"UPDATE {table} SET {set_clause} WHERE id=?",
                    (*data.values(), int(rid)))
            flash("已更新", "success")
        else:
            cols = ", ".join(data.keys())
            ph = ", ".join(["?"] * len(data))
            execute(f"INSERT INTO {table} ({cols}) VALUES ({ph})", tuple(data.values()))
            flash("已新增", "success")
        return redirect(url_for(list_url))
    items = query(f"SELECT * FROM {table} ORDER BY {order}")
    return render_template(form_template, items=items)


@app.route("/admin/sliders", methods=["GET", "POST"])
@login_required
def admin_sliders():
    return generic_crud("sliders", ["title", "subtitle", "link", "sort"],
                        "admin_sliders", "admin/sliders.html", ["image"])


@app.route("/admin/partners", methods=["GET", "POST"])
@login_required
def admin_partners():
    return generic_crud("partners", ["name", "url", "sort"],
                        "admin_partners", "admin/partners.html", ["logo"])


@app.route("/admin/products", methods=["GET", "POST"])
@login_required
def admin_products():
    if request.method == "POST":
        action = request.form.get("action", "save")
        rid = request.form.get("id")
        data = {c: request.form.get(c, "") for c in
                ["title", "summary", "icon", "content", "sort"]}
        f = request.files.get("image")
        if f and f.filename:
            url = save_upload(f)
            if url:
                data["image"] = url
        if action == "delete" and rid:
            execute("DELETE FROM products WHERE id=?", (int(rid),))
            flash("已删除", "success")
            return redirect(url_for("admin_products"))
        if rid:
            keys = list(data.keys())
            set_clause = ", ".join(f"{c}=?" for c in keys)
            execute(f"UPDATE products SET {set_clause} WHERE id=?",
                    (*[data[k] for k in keys], int(rid)))
            flash("已更新", "success")
        else:
            cols = ", ".join(data.keys())
            ph = ", ".join(["?"] * len(data))
            execute(f"INSERT INTO products ({cols}) VALUES ({ph})", tuple(data.values()))
            flash("已新增", "success")
        return redirect(url_for("admin_products"))
    items = query("SELECT * FROM products ORDER BY sort ASC, id ASC")
    return render_template("admin/products.html", items=items)


@app.route("/admin/offices", methods=["GET", "POST"])
@login_required
def admin_offices():
    return generic_crud("offices", ["name", "region", "lat", "lng", "sort"],
                        "admin_offices", "admin/offices.html")


@app.route("/admin/certifications", methods=["GET", "POST"])
@login_required
def admin_certifications():
    return generic_crud("certifications", ["title", "description", "sort"],
                        "admin_certifications", "admin/certifications.html", ["image"])


@app.route("/admin/testimonials", methods=["GET", "POST"])
@login_required
def admin_testimonials():
    return generic_crud("testimonials", ["name", "title", "content", "sort"],
                        "admin_testimonials", "admin/testimonials.html", ["avatar"])


@app.route("/admin/friendlinks", methods=["GET", "POST"])
@login_required
def admin_friendlinks():
    return generic_crud("friendlinks", ["name", "url", "sort"],
                        "admin_friendlinks", "admin/friendlinks.html")


@app.route("/admin/news", methods=["GET", "POST"])
@login_required
def admin_news():
    if request.method == "POST":
        action = request.form.get("action", "save")
        rid = request.form.get("id")
        data = {c: request.form.get(c, "") for c in
                ["title", "summary", "content"]}
        f = request.files.get("image")
        if f and f.filename:
            url = save_upload(f)
            if url:
                data["image"] = url
        if action == "delete" and rid:
            execute("DELETE FROM news WHERE id=?", (int(rid),))
            flash("已删除", "success")
            return redirect(url_for("admin_news"))
        if rid:
            keys = list(data.keys())
            set_clause = ", ".join(f"{c}=?" for c in keys)
            execute(f"UPDATE news SET {set_clause} WHERE id=?",
                    (*[data[k] for k in keys], int(rid)))
            flash("已更新", "success")
        else:
            cols = ", ".join(data.keys())
            ph = ", ".join(["?"] * len(data))
            execute(f"INSERT INTO news ({cols}) VALUES ({ph})", tuple(data.values()))
            flash("已新增", "success")
        return redirect(url_for("admin_news"))
    items = query("SELECT * FROM news ORDER BY created_at DESC")
    return render_template("admin/news.html", items=items)


@app.route("/admin/password", methods=["GET", "POST"])
@login_required
def admin_password():
    if request.method == "POST":
        np = request.form.get("password", "").strip()
        if len(np) < 4:
            flash("密码长度至少 4 位", "error")
        else:
            execute("UPDATE users SET password=? WHERE username=?",
                    (np, session["admin"]))
            flash("密码已修改", "success")
        return redirect(url_for("admin_password"))
    return render_template("admin/password.html")


# ---------- Run ----------
if __name__ == "__main__":
    if not os.path.exists(DB_PATH):
        with app.app_context():
            init_db()
    else:
        with app.app_context():
            # ensure tables exist (if db exists but empty)
            try:
                query("SELECT 1 FROM settings LIMIT 1")
            except Exception:
                init_db()
    app.run(host="0.0.0.0", port=5000, debug=True)

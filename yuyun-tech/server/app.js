const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;
const DATA_DIR = path.join(__dirname, 'data');

app.use(cors());
app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, '..', 'public')));
app.use('/admin', express.static(path.join(__dirname, '..', 'admin')));

if (!fs.existsSync(DATA_DIR)) {
    fs.mkdirSync(DATA_DIR, { recursive: true });
}

const ADMIN_USER = { username: 'admin', password: 'admin123' };
const sessions = {};

const dataFiles = {
    site: 'site.json',
    home: 'home.json',
    carousel: 'carousel.json',
    products: 'products.json',
    partners: 'partners.json',
    locations: 'locations.json',
    popup: 'popup.json',
    footer: 'footer.json',
    about: 'about.json',
    contact: 'contact.json'
};

Object.values(dataFiles).forEach((file) => {
    const filePath = path.join(DATA_DIR, file);
    if (!fs.existsSync(filePath)) {
        fs.writeFileSync(filePath, '{}', 'utf8');
    }
});

function getData(key) {
    const file = dataFiles[key];
    if (!file) return null;
    try {
        return JSON.parse(fs.readFileSync(path.join(DATA_DIR, file), 'utf8'));
    } catch (e) {
        return null;
    }
}

function saveData(key, data) {
    const file = dataFiles[key];
    if (!file) return false;
    try {
        fs.writeFileSync(path.join(DATA_DIR, file), JSON.stringify(data, null, 2), 'utf8');
        return true;
    } catch (e) {
        return false;
    }
}

app.post('/api/admin/login', (req, res) => {
    const { username, password } = req.body;
    if (username === ADMIN_USER.username && password === ADMIN_USER.password) {
        const token = Date.now() + '_' + Math.random().toString(36).substr(2);
        sessions[token] = { username, time: Date.now() };
        res.json({ success: true, token });
    } else {
        res.status(401).json({ success: false, message: '账号或密码错误' });
    }
});

function authMiddleware(req, res, next) {
    const token = req.headers['x-admin-token'] || req.query.token;
    if (token && sessions[token]) {
        next();
    } else {
        res.status(401).json({ success: false, message: '未授权访问' });
    }
}

app.get('/api/config/:key', (req, res) => {
    const data = getData(req.params.key);
    if (data === null) {
        res.status(404).json({ success: false });
    } else {
        res.json({ success: true, data });
    }
});

app.post('/api/config/:key', authMiddleware, (req, res) => {
    if (saveData(req.params.key, req.body)) {
        res.json({ success: true });
    } else {
        res.status(500).json({ success: false });
    }
});

app.get('/api/admin/check', authMiddleware, (req, res) => {
    res.json({ success: true });
});

app.listen(PORT, () => {
    console.log('语云科技官网已启动，端口：', PORT);
});

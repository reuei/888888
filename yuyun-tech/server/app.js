const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json({ limit: '10mb' }));
app.use(bodyParser.urlencoded({ extended: true, limit: '10mb' }));

app.use(express.static(path.join(__dirname, '..', 'public')));
app.use('/admin', express.static(path.join(__dirname, '..', 'admin')));

const DATA_DIR = path.join(__dirname, 'data');

if (!fs.existsSync(DATA_DIR)) {
    fs.mkdirSync(DATA_DIR, { recursive: true });
}

function readJSON(file) {
    try {
        const filePath = path.join(DATA_DIR, file);
        if (!fs.existsSync(filePath)) return null;
        return JSON.parse(fs.readFileSync(filePath, 'utf-8'));
    } catch (e) {
        return null;
    }
}

function writeJSON(file, data) {
    try {
        const filePath = path.join(DATA_DIR, file);
        fs.writeFileSync(filePath, JSON.stringify(data, null, 2), 'utf-8');
        return true;
    } catch (e) {
        return false;
    }
}

app.get('/api/config/site', (req, res) => {
    const data = readJSON('site.json');
    res.json(data || {});
});

app.get('/api/config/home', (req, res) => {
    const data = readJSON('home.json');
    res.json(data || {});
});

app.get('/api/config/products', (req, res) => {
    const data = readJSON('products.json');
    res.json(data || {});
});

app.get('/api/config/partners', (req, res) => {
    const data = readJSON('partners.json');
    res.json(data || {});
});

app.get('/api/config/about', (req, res) => {
    const data = readJSON('about.json');
    res.json(data || {});
});

app.get('/api/config/contact', (req, res) => {
    const data = readJSON('contact.json');
    res.json(data || {});
});

app.get('/api/config/footer', (req, res) => {
    const data = readJSON('footer.json');
    res.json(data || {});
});

app.get('/api/config/navbar', (req, res) => {
    const data = readJSON('navbar.json');
    res.json(data || {});
});

app.get('/api/config/popup', (req, res) => {
    const data = readJSON('popup.json');
    res.json(data || {});
});

app.get('/api/config/sidebar', (req, res) => {
    const data = readJSON('sidebar.json');
    res.json(data || {});
});

app.post('/api/config/:section', (req, res) => {
    const section = req.params.section;
    const allowed = ['site', 'home', 'products', 'partners', 'about', 'contact', 'footer', 'navbar', 'popup', 'sidebar'];
    if (!allowed.includes(section)) {
        return res.json({ success: false, message: 'Invalid section' });
    }
    const result = writeJSON(section + '.json', req.body);
    res.json({ success: result });
});

app.post('/api/config/footer/phone', (req, res) => {
    const data = readJSON('footer.json') || {};
    data.salesPhone = req.body.phone || data.salesPhone;
    const result = writeJSON('footer.json', data);
    res.json({ success: result, data });
});

app.post('/api/config/home/banner', (req, res) => {
    const data = readJSON('home.json') || {};
    data.banners = req.body.banners || data.banners;
    const result = writeJSON('home.json', data);
    res.json({ success: result, data });
});

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '..', 'public', 'index.html'));
});

app.listen(PORT, () => {
    console.log(`语云科技官网已启动: http://localhost:${PORT}`);
});

module.exports = app;

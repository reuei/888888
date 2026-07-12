        <style>
            :root{
                --gov-red:#9a0006;
                --gov-red-dark:#4a0000;
                --gov-red-deep:#2e0000;
                --gov-gold:#d4af37;
                --gov-gold-light:#f0d878;
                --gov-gold-deep:#b8941f;
            }
            .illustration-3d{
                perspective:1100px;
                display:flex;
                justify-content:center;
                align-items:flex-end;
                gap:28px;
                padding:36px 0 28px;
                margin-bottom:6px;
                position:relative;
            }
            .illustration-3d::before{
                content:"";
                position:absolute;
                left:50%;top:50%;
                width:560px;height:200px;
                transform:translate(-50%,-50%) rotateX(72deg);
                background:radial-gradient(ellipse at center,rgba(212,175,55,.22),transparent 70%);
                filter:blur(6px);
                pointer-events:none;
            }
            .illust-card{
                width:96px;
                height:128px;
                border-radius:10px;
                transform-style:preserve-3d;
                background:linear-gradient(160deg,var(--gov-red) 0%,var(--gov-red-dark) 70%,var(--gov-red-deep) 100%);
                border:1px solid rgba(212,175,55,.65);
                box-shadow:
                    0 18px 30px rgba(46,0,0,.35),
                    inset 0 1px 0 rgba(240,216,120,.35);
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                color:var(--gov-gold-light);
                animation:float3d 5s ease-in-out infinite;
                animation-delay:var(--d,0s);
                position:relative;
                overflow:hidden;
            }
            .illust-card::after{
                content:"";
                position:absolute;inset:6px;
                border:1px dashed rgba(212,175,55,.45);
                border-radius:7px;
                pointer-events:none;
            }
            .illust-card .illust-svg{
                width:34px;height:34px;
                margin-bottom:6px;
                color:var(--gov-gold-light);
                filter:drop-shadow(0 2px 3px rgba(0,0,0,.4));
            }
            .illust-card .illust-char{
                font-size:22px;
                font-weight:700;
                letter-spacing:2px;
                color:#fff;
                text-shadow:0 1px 3px rgba(0,0,0,.5);
            }
            .illust-card .illust-en{
                font-size:9px;
                letter-spacing:2px;
                margin-top:3px;
                color:rgba(240,216,120,.85);
            }
            .illust-card:nth-child(odd){transform:rotateY(-12deg) rotateX(6deg);}
            .illust-card:nth-child(even){transform:rotateY(12deg) rotateX(6deg);}
            @keyframes float3d{
                0%,100%{transform:translateY(0) rotateY(var(--ry,-12deg)) rotateX(6deg);}
                50%{transform:translateY(-14px) rotateY(calc(var(--ry,-12deg) * -1)) rotateX(-4deg);}
            }
            .illust-card:nth-child(1){--ry:-12deg;--d:0s;}
            .illust-card:nth-child(2){--ry:12deg;--d:.5s;}
            .illust-card:nth-child(3){--ry:-12deg;--d:1s;}
            .illust-card:nth-child(4){--ry:12deg;--d:1.5s;}
            @media(max-width:600px){
                .illustration-3d{gap:14px;padding:24px 0 16px;}
                .illust-card{width:68px;height:92px;}
                .illust-card .illust-svg{width:24px;height:24px;}
                .illust-card .illust-char{font-size:17px;}
                .illust-card .illust-en{font-size:7px;}
            }
            .site-footer{
                background:
                    radial-gradient(circle at 12% 0%,rgba(212,175,55,.10),transparent 45%),
                    linear-gradient(180deg,var(--gov-red-dark) 0%,var(--gov-red-deep) 100%);
                color:#e9d9b0;
                border-top:3px solid var(--gov-gold);
                position:relative;
            }
            .site-footer::before{
                content:"";
                position:absolute;left:0;right:0;top:0;height:3px;
                background:repeating-linear-gradient(90deg,var(--gov-gold) 0 18px,transparent 18px 30px);
                opacity:.45;
            }
            .footer-wrap{
                display:grid;
                grid-template-columns:repeat(4,1fr);
                gap:30px;
                padding:34px 0 26px;
            }
            .footer-col h4{
                color:var(--gov-gold-light);
                font-size:16px;
                letter-spacing:3px;
                margin-bottom:14px;
                padding-bottom:8px;
                border-bottom:1px solid rgba(212,175,55,.4);
                display:flex;
                align-items:center;
                gap:8px;
            }
            .footer-col h4 .footer-svg{
                width:18px;height:18px;
                color:var(--gov-gold);
                flex-shrink:0;
            }
            .footer-col ul li{margin-bottom:9px;}
            .footer-col ul li a{
                color:#e9d9b0;
                font-size:13px;
                display:inline-flex;
                align-items:center;
                gap:6px;
                transition:color .2s,padding-left .2s;
            }
            .footer-col ul li a::before{
                content:"";
                display:inline-block;
                width:5px;height:5px;
                background:var(--gov-gold);
                transform:rotate(45deg);
                flex-shrink:0;
            }
            .footer-col ul li a:hover{
                color:#fff;
                padding-left:4px;
            }
            .footer-col .footer-image{
                border:2px solid var(--gov-gold);
                padding:6px;
                background:#fff;
                border-radius:6px;
                box-shadow:0 0 12px rgba(212,175,55,.3);
                width:fit-content;
            }
            .footer-col .footer-image img{display:block;width:120px;height:120px;}
            .footer-bottom{
                border-top:1px solid rgba(212,175,55,.3);
                padding:18px 0;
                text-align:center;
                color:rgba(240,216,120,.8);
                font-size:13px;
            }
            .footer-bottom p{margin:4px 0;}
            .footer-bottom a{color:var(--gov-gold-light);}
            .footer-bottom a:hover{color:#fff;text-decoration:underline;}
            @media(max-width:768px){
                .footer-wrap{grid-template-columns:repeat(2,1fr);gap:20px;}
            }
            @media(max-width:480px){
                .footer-wrap{grid-template-columns:1fr;}
            }
        </style>

        <footer class="site-footer">
            <div class="container">
                <div class="illustration-3d">
                    <div class="illust-card" style="--d:0s">
                        <svg class="illust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l2.4 6.9H22l-6 4.4 2.3 7.1L12 16.2 5.7 20.4 8 13.3 2 8.9h7.6z"/></svg>
                        <span class="illust-char">廉</span>
                        <span class="illust-en">INTEGRITY</span>
                    </div>
                    <div class="illust-card" style="--d:.5s">
                        <svg class="illust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="3" x2="12" y2="21"/><line x1="7" y1="21" x2="17" y2="21"/><line x1="5" y1="7" x2="19" y2="7"/><path d="M5 7L2 13a3 3 0 0 0 6 0L5 7z"/><path d="M19 7l-3 6a3 3 0 0 0 6 0l-3-6z"/></svg>
                        <span class="illust-char">正</span>
                        <span class="illust-en">JUSTICE</span>
                    </div>
                    <div class="illust-card" style="--d:1s">
                        <svg class="illust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="9" y1="12" x2="15" y2="12"/></svg>
                        <span class="illust-char">纪</span>
                        <span class="illust-en">DISCIPLINE</span>
                    </div>
                    <div class="illust-card" style="--d:1.5s">
                        <svg class="illust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="11"/><line x1="11" y1="11" x2="13" y2="11"/></svg>
                        <span class="illust-char">察</span>
                        <span class="illust-en">SUPERVISION</span>
                    </div>
                </div>

                <div class="footer-wrap">
                    <div class="footer-col">
                        <h4>
                            <svg class="footer-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            关于我们
                        </h4>
                        <ul>
                            <li><a href="#">网站简介</a></li>
                            <li><a href="#">联系方式</a></li>
                            <li><a href="#">工作邮箱</a></li>
                            <li><a href="#">设为首页</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>
                            <svg class="footer-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h12"/></svg>
                            快速链接
                        </h4>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=yaowen">要闻动态</a></li>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=shencha">审查调查</a></li>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=xunshi">巡视巡察</a></li>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=fagui">党纪法规</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>
                            <svg class="footer-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            互动交流
                        </h4>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>report.php">监督举报</a></li>
                            <li><a href="<?php echo BASE_URL; ?>message.php">留言板</a></li>
                            <li><a href="#">我要投稿</a></li>
                            <li><a href="#">意见建议</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>
                            <svg class="footer-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/><rect x="18" y="18" width="3" height="3"/></svg>
                            关注我们
                        </h4>
                        <?php
                        $footerImage = getSetting('footer_image', '');
                        if ($footerImage):
                        ?>
                            <div class="footer-image">
                                <img src="<?php echo BASE_URL . UPLOAD_URL . e($footerImage); ?>" alt="关注二维码">
                            </div>
                        <?php else: ?>
                            <p style="font-size:12px; color:rgba(240,216,120,.8);">扫码关注官方微信</p>
                            <div style="width:120px; height:120px; background:rgba(255,255,255,.08); border:2px dashed rgba(212,175,55,.5); border-radius:6px; margin-top:10px; display:flex; align-items:center; justify-content:center; color:rgba(240,216,120,.6); font-size:12px;">
                                二维码
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p><?php echo e(getSetting('footer_copyright', '© ' . date('Y') . ' 清廉在线 版权所有')); ?></p>
                    <?php if (getSetting('icp', '')): ?>
                        <p><a href="https://beian.miit.gov.cn/" target="_blank"><?php echo e(getSetting('icp')); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </footer>
    </body>
</html>

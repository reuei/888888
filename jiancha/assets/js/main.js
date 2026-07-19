(function(){
'use strict';

/* Mobile Nav */
var toggle=document.getElementById('navToggle');
var links=document.getElementById('navLinks');
if(toggle&&links){
  toggle.addEventListener('click',function(){
    links.classList.toggle('open');
    var spans=toggle.querySelectorAll('span');
    if(links.classList.contains('open')){
      spans[0].style.transform='rotate(45deg) translate(5px,5px)';
      spans[1].style.opacity='0';
      spans[2].style.transform='rotate(-45deg) translate(5px,-5px)';
    }else{
      spans[0].style.transform='';spans[1].style.opacity='';spans[2].style.transform='';
    }
  });
  document.addEventListener('click',function(e){
    if(!toggle.contains(e.target)&&!links.contains(e.target)&&links.classList.contains('open')){
      links.classList.remove('open');
      var spans=toggle.querySelectorAll('span');
      spans[0].style.transform='';spans[1].style.opacity='';spans[2].style.transform='';
    }
  });
}

/* Slider */
function initSlider(){
  var slider=document.getElementById('slider');
  if(!slider)return;
  var track=document.getElementById('sliderTrack');
  var bar=document.getElementById('sliderBar');
  var prev=document.getElementById('sliderPrev');
  var next=document.getElementById('sliderNext');
  if(!track)return;
  var items=track.querySelectorAll('.slider-item');
  if(items.length<=1)return;
  var idx=0,interval,dur=5000,elapsed=0,start=Date.now(),running=true;
  function go(i){
    idx=(i+items.length)%items.length;
    track.style.transform='translateX('+(-idx*100)+'%)';
    elapsed=0;start=Date.now();
  }
  function tick(){
    if(!running)return;
    elapsed=Date.now()-start;
    var pct=Math.min(elapsed/dur*100,100);
    if(bar)bar.style.width=pct+'%';
    if(elapsed>=dur){go(idx+1);}
    requestAnimationFrame(tick);
  }
  if(prev)prev.addEventListener('click',function(){go(idx-1);elapsed=0;start=Date.now();});
  if(next)next.addEventListener('click',function(){go(idx+1);elapsed=0;start=Date.now();});
  var sx;
  slider.addEventListener('touchstart',function(e){sx=e.touches[0].clientX;},{passive:true});
  slider.addEventListener('touchend',function(e){
    var dx=e.changedTouches[0].clientX-sx;
    if(Math.abs(dx)>40){dx<0?go(idx+1):go(idx-1);elapsed=0;start=Date.now();}
  },{passive:true});
  slider.addEventListener('mouseenter',function(){running=false;});
  slider.addEventListener('mouseleave',function(){running=true;start=Date.now()-elapsed;tick();});
  tick();
}

/* Form Validation */
function initForms(){
  var rules={
    username:/^[\w\x{4e00}-\x{9fa5}]{3,20}$/u,
    password:/^(?=.*[A-Za-z]|.*\d).{6,}$/,
    confirm_password:function(v){return v===((this.form.querySelector('[name="password"]')||{}).value||'');},
    email:/^$|^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    phone:/^$|^1[3-9]\d{9}$/,
    title:/^.{4,}$/,
    content:/^.{10,}$/
  };
  var msgs={
    username:'3-20位字母/数字/中文',password:'至少6位，不能纯数字',
    confirm_password:'两次密码不一致',email:'邮箱格式错误',phone:'手机号格式错误',
    title:'至少4个字符',content:'至少10个字符'
  };
  document.querySelectorAll('form').forEach(function(form){
    form.querySelectorAll('[data-v]').forEach(function(input){
      input.addEventListener('blur',function(){
        var type=input.getAttribute('data-v');
        var val=input.value.trim();
        var rule=rules[type];
        var ok=true;
        if(!val&&type!=='confirm_password'){ok=true;}
        else if(typeof rule==='function'){ok=rule.call({form:form},val);}
        else if(rule instanceof RegExp){ok=rule.test(val);}
        input.classList.remove('ok','err');
        if(val){input.classList.add(ok?'ok':'err');}
        var tip=input.parentNode.querySelector('.field-tip');
        if(tip)tip.remove();
        if(!ok&&val){
          var d=document.createElement('div');
          d.className='field-tip';
          d.style.cssText='color:var(--red);font-size:12px;margin-top:4px';
          d.textContent=msgs[type]||'格式错误';
          input.parentNode.appendChild(d);
        }
      });
    });
    form.addEventListener('submit',function(e){
      var firstErr=form.querySelector('.err');
      if(firstErr){e.preventDefault();firstErr.focus();showToast('请检查表单填写是否正确');}
    });
  });
}

/* Toast */
window.showToast=function(msg,type){
  var t=document.getElementById('toast');
  if(!t)return;
  t.textContent=msg;
  t.style.borderColor=type==='error'?'rgba(239,68,68,0.3)':type==='success'?'rgba(34,197,94,0.3)':'var(--border)';
  t.classList.add('show');
  setTimeout(function(){t.classList.remove('show');},3000);
};

/* Modal */
window.showModal=function(title,body,cb){
  var m=document.getElementById('modal');
  var b=document.getElementById('modalBackdrop');
  if(!m||!b)return;
  document.getElementById('modalTitle').textContent=title||'提示';
  document.getElementById('modalBody').innerHTML=body||'';
  m.classList.add('open');
  b.classList.add('open');
  var cfm=document.getElementById('modalConfirm');
  cfm.onclick=function(){m.classList.remove('open');b.classList.remove('open');if(cb)cb();};
};
var mc=document.getElementById('modalClose');
var mb=document.getElementById('modalBackdrop');
if(mc)mc.addEventListener('click',function(){document.getElementById('modal').classList.remove('open');mb.classList.remove('open');});
if(mb)mb.addEventListener('click',function(){document.getElementById('modal').classList.remove('open');mb.classList.remove('open');});

/* Admin */
function initAdmin(){
  var btn=document.getElementById('adminMenuBtn');
  var side=document.getElementById('adminSide');
  if(btn&&side){
    btn.addEventListener('click',function(){side.classList.toggle('open');});
  }
}

/* Init */
document.addEventListener('DOMContentLoaded',function(){
  initSlider();
  initForms();
  initAdmin();
});
})();

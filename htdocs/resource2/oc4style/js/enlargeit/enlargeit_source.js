/*  This comment MUST stay intact for legal use, so don't remove it. EnlargeIt! 
v1.1 - (c) 2008 Timo Sack - http://enlargeit.timos-welt.de This program is free 
software: you can redistribute it and/or modify it under the terms of the GNU 
General Public License as published by the Free Software Foundation, either 
version 3 of the License, or (at your option) any later version. See LICENSE.TXT 
for details. */

// modify these
var enl_gifpath='./resource2/ocstyle/js/enlargeit/'; // path to graphics
var enl_brdsize=6;    // border thickness (5-30)
var enl_brdcolor='#000';   // border color (white if empty)
var enl_brdbck='';     // border background pic, '' for no pic
var enl_brdround=0;    // use rounded borders (Mozilla/Safari only)
var enl_maxstep=6;     // ani steps (10-30)
  // Speed of ani steps ist very dependend on the machine and browser. 
  // Must be kept relatively slow to be bearable in slow environments.
var enl_speed=5;       // time between steps
var enl_ani=2;         // 0=no,1=fade,2=glide,3=bumpglide,4=smoothglide,5=expglide
var enl_opaglide=1;    // glide transparency
var enl_shadow=1;      // shadow under border
var enl_shadowsize=1;  // size of shadow right/bottom (0-20)
var enl_shadowcolor='';// shadow color (empty: black)
var enl_shadowintens=15;// shadow intensity (5-30)
var enl_dark=2;        // darken screen (0=off/1=on/2=keep dark when nav)
var enl_darkprct=30;   // how dark the screen should be (0-100)
var enl_darksteps=2;   // how long darkening should take
var enl_center=0;      // center enlarged pic on screen
var enl_drgdrop=1;     // enable drag&drop for pics
var enl_preload=0;     // preload next/prev pic
var enl_titlebar=1;    // show pic title bar
var enl_keynav=1;      // key navigation
var enl_wheelnav=1;    // mouse wheel navigation
var enl_titletxtcol='#ddd';// color of title bar text (empty: dark grey)
var enl_ajaxcolor='#000';  // background color for AJAX (empty: light grey)
var enl_usecounter=0;  // hidden call of counter page
var enl_counterurl=''; // base URL of counter page
var enl_btnact='bact.png';               // active buttons
var enl_btninact='binact.png';           // inactive buttons
var enl_pluscur='pluscur.cur';           // mouse cursor of thumbnail
var enl_minuscur='minuscur.cur';         // mouse cursor of enlarged image
var enl_noflash='No flash plugin found!';// msg if no flash plugin found
var enl_canceltext='Click to cancel';    // tooltip to cancel loading

// don't modify next line
var enl_buttonurl = new Array(),enl_buttontxt = new Array(),enl_buttonoff = new Array();
// define your buttons here
enl_buttonurl[0] = 'prev';
enl_buttontxt[0] = '';
enl_buttonoff[0] = -180;
enl_buttonurl[1] = 'next';
enl_buttontxt[1] = '';
enl_buttonoff[1] = -198;
enl_buttonurl[2] = 'close';
enl_buttontxt[2] = '';
enl_buttonoff[2] = -126;

// stuff to leave alone

// global vars
var enl_prldimg=new Array(),enl_button=new Array(),enl_stopload=0;
var enl_butact,enl_butinact,enl_btnheight,enl_prldcnt=0,enl_darkened=0;
var enl_nn6=document.getElementById&&!document.all;enl_drgmode=false;
var enl_drgelem,enl_mvcnt=0,enl_isie=window.ActiveXObject,enl_hasmvd=false;
var enl_brwsx,enl_brwsy,enl_scrollx,enl_scrolly,enl_infront='';
var enl_firstcall=0,enl_inprogress=0,enl_zcnt=9700,enl_inmax=0,enl_ie6=0;
var enl_request=false,enl_request2=false,enl_keepblack=0,enl_brdbckpic;

// init function at page load or first enlarge
function enl_init()
{
  if (!enl_firstcall)
  {
    enl_firstcall = 1;
    
    // parameter corrections
    if (typeof enl_buttonpress == 'undefined') enl_drgdrop = 0;
    if (typeof enl_ajax == 'undefined') enl_usecounter = 0;
    if (typeof enl_darken == 'undefined') enl_dark = 0;
    if (typeof enl_dropshadow == 'undefined') enl_shadow = 0;
    if (typeof enl_wheel == 'undefined') enl_wheelnav = 0;
    if (typeof enl_keynavi == 'undefined') enl_keynav = 0;
    if (typeof enl_mktitlebar == 'undefined') enl_titlebar = 0;
    else if (enl_buttonurl.length) enl_titlebar = 1;
    if (typeof enl_dofadein == 'undefined' && enl_ani == 1) enl_ani = 2;
    if (typeof enl_doglidein == 'undefined' && enl_ani > 1) enl_ani = 0;
    var enl_i = 0;
    
    // prepare ajax
    if (typeof enl_ajax != 'undefined') enl_ajaxprepare();
    
    // button img
    if (enl_titlebar) {
      enl_preloadit(enl_gifpath+enl_btnact);
      enl_butact = enl_prldimg[enl_prldcnt];
      enl_preloadit(enl_gifpath+enl_btninact);
      enl_butinact = enl_prldimg[enl_prldcnt];
    }

    // background img
    if (enl_brdbck) enl_preloadit(enl_gifpath+enl_brdbck);
    enl_brdbckpic = enl_prldimg[enl_prldcnt];

    // loader div
    enl_ldr = enl_mkdiv('enl_ldr');
    enl_ldr.style.zIndex = 9999;
    enl_ldrgif = new Image();
    enl_ldrgif.src = enl_gifpath+'loader.gif';
    enl_ldrgif.style.borderWidth = '1px';
    enl_ldrgif.style.borderStyle = 'solid';
    enl_ldrgif.style.borderColor = 'black';
    enl_ldrgif.id = 'enl_ldrgif';
    enl_ldr.appendChild(enl_ldrgif);

    // border div
    enl_brdm = enl_mkdiv('enl_brd');
    enl_brdm.name = 'ajax';
    enl_brdm.style.backgroundColor = (enl_brdcolor) ? enl_brdcolor : '#ffffff';
    if (enl_brdbck) enl_brdm.style.backgroundImage = 'url('+enl_gifpath+enl_brdbck+')';
    if (enl_brdround && !enl_brdbck)
    {
      enl_brdm.style.MozBorderRadius = enl_brdsize+'px';
      enl_brdm.style.khtmlBorderRadius = enl_brdsize+'px';
    }

    // shadow div
    if (enl_shadow)
    {
        enl_shdm = enl_mkdiv('enl_shd');
        enl_shdm.style.backgroundColor = (enl_shadowcolor) ? enl_shadowcolor : 'black';
        enl_setopa(enl_shdm,enl_shadowintens);
        if (enl_brdround && !enl_brdbck)
        {
          enl_shdm.style.MozBorderRadius = eval(enl_brdsize+1)+'px';
          enl_shdm.style.khtmlBorderRadius = eval(enl_brdsize+1)+'px';
        }
    }
    if (enl_dark) enl_darkenprepare();
    if (enl_keynav)
    {
      document.onkeyup = enl_keynavi;
      document.onkeydown = null;
    }
    enl_imglist = document.getElementsByTagName('img');
    
    // set mouse plus cursor, assign IDs, fix IE alt tooltip bug for thumbs
    if (typeof document.body.style.maxHeight == 'undefined') enl_ie6 = 1;
    var enl_ui;
    for (var enl_i=0; enl_i<enl_imglist.length; enl_i++)
    {
      if (typeof enl_imglist[enl_i].onclick == 'function') {
        enl_ui = eval(enl_imglist[enl_i].onclick).toString();
        if (enl_ui.search(/enlarge/) != -1)
        {
          enl_imglist[enl_i].title = '';
          enl_imglist[enl_i].saveClassName = enl_imglist[enl_i].className;
          if (enl_isie) enl_imglist[enl_i].galleryimg = 'no';
          if (!enl_imglist[enl_i].id) enl_imglist[enl_i].id = 'enl_autoid'+enl_i;
          enl_setcur(enl_imglist[enl_i],enl_pluscur,'pointer','hand');
        }
      }
    }
    enl_firstcall = 2;
    enl_timetowait = (enl_isie) ? 750 : 100;
    if (typeof enl_openpic != 'undefined') setTimeout('enl_openthepic("'+enl_openpic+'")' ,enl_timetowait);
  }
}

// open a pic by pic.id
function enl_openthepic(enl_toopen)
{
  enl_img = enl_geto(enl_toopen);
  enlarge(enl_img);
}

// set position of object
function enl_setpos(enl_obj,enl_posx,enl_posy,enl_w,enl_h)
{
  enl_obj.style.left = enl_posx+'px';
  enl_obj.style.top = enl_posy+'px';
  if (enl_w) {
    enl_obj.style.width = enl_w+'px';
    enl_obj.style.height = enl_h+'px';
  }
}

// set opacity of object
function enl_setopa(enl_obj,enl_opval) {
  enl_obj.style.opacity=enl_opval/100;
  enl_obj.style.MozOpacity=enl_opval/100;
  enl_obj.style.filter = "alpha(opacity="+enl_opval+")";
}

// get object by id
function enl_geto(enl_imgid)
{
  return document.getElementById(enl_imgid);
}

// preload image
function enl_preloadit(enl_picpath)
{
  enl_prldcnt +=1;
  enl_prldimg[enl_prldcnt] = new Image();
  if (enl_picpath.slice(3,5) != '::' ) enl_prldimg[enl_prldcnt].src = enl_picpath;
  else if (!enl_isie) enl_prldimg[enl_prldcnt].src = enl_picpath.split('::')[1];
}

// show object
function enl_visible(enl_obj)
{
  enl_obj.style.visibility = 'visible';
}

// hide object
function enl_hide(enl_obj)
{
  enl_obj.style.visibility = 'hidden';
}

// create div
function enl_mkdiv(enl_divname)
{
  enl_div = document.createElement("div");
  enl_hide (enl_div);
  enl_div.id = enl_divname;
  enl_div.style.position = 'absolute';
  enl_setpos(enl_div,-5000,0,0,0);
  document.body.appendChild(enl_div);
  return enl_div;
}

// get viewport
function enl_getbrwsxy()
{
 if (typeof window.innerWidth != 'undefined')
 {
   enl_brwsx = window.innerWidth - 10;
   enl_brwsy = window.innerHeight;
 }
 else if (typeof document.documentElement  != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0)
 {
  enl_brwsx = document.documentElement.clientWidth;
  enl_brwsy = document.documentElement.clientHeight;
 }
 else
 {
   enl_brwsx = document.getElementsByTagName('body')[0].clientWidth;
   enl_brwsy = document.getElementsByTagName('body')[0].clientHeight;
 }
 enl_scrolly = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
 enl_scrollx = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0;
}

// start/stop slider plug if exists
function enl_ctlslid(enl_i)
{
  if (typeof realcopyspeed != 'undefined') copyspeed=(enl_i?realcopyspeed:0);
}

// get top, left, width and height
function enl_coord(enl_el)
{
  var enl_values = { top:0, left:0, width:0, height:0 };
  if(!enl_el) return enl_values;
  else if(typeof enl_el == 'string' ) enl_el = enl_geto(enl_el);
  if( typeof enl_el != 'object' ) return enl_values;
  if(typeof enl_el.offsetTop != 'undefined') {
    enl_values.height = enl_el.offsetHeight;
    enl_values.width = enl_el.offsetWidth; enl_values.left = enl_el.top = 0;
    while (enl_el && enl_el.tagName != 'BODY') {
  enl_values.top  += parseInt( enl_el.offsetTop ); enl_values.left += parseInt( enl_el.offsetLeft ); enl_el = enl_el.offsetParent; } }
  return enl_values;
}

// set mouse cursor
function enl_setcur(enl_obj,enl_curstr1,enl_curstr2,enl_curstr3)
{
  if (window.opera || (!enl_curstr1 && !enl_ie6)) {try {enl_obj.style.cursor = enl_curstr2;} catch(enl_err) {}}
  else if (enl_ie6) {try {enl_obj.style.cursor = enl_curstr3;} catch(enl_err) {}}
  else {try {enl_obj.style.cursor = 'url('+enl_gifpath+enl_curstr1+'),'+enl_curstr2;} catch(enl_err) {}}
}

// onmouse events for drag, preload
function enl_makedraggable(enl_imgid)
{
  enl_infront = enl_imgid;
  enl_img = enl_geto(enl_imgid);
  if (enl_img.issmaller == 1 && enl_isie) enl_img.style.msInterpolationMode = 'bicubic';
  enl_orig = enl_geto(enl_img.orig);
  enl_setcur(enl_orig,'','default','default');
  if (enl_drgdrop)
  { enl_img.onmousedown=enl_buttonpress;
    enl_img.onmouseup=enl_enddrag; }
  else if (enl_img.ispic || !enl_titlebar) enl_img.onclick = function() { enl_shrink(enl_imgid); };
  if (!enl_inmax && enl_usecounter) setTimeout('enl_count("'+enl_orig.id+'")' ,40);
  if (enl_drgdrop) enl_setcur(enl_img,enl_minuscur,'move','move');
  else enl_setcur(enl_img,enl_minuscur,'pointer','hand');
  enl_inprogress=0;
  enl_ctlslid(1);
  if (enl_preload)
  {
    for(var enl_i=0; enl_i<2; enl_i++)
    {
      enl_nextpic = enl_getnext(enl_imgid,enl_i);
      if (enl_nextpic)
      {
        enl_pictoget = enl_nextpic.getAttribute('longdesc');
        setTimeout('enl_preloadit("'+enl_pictoget+'")' ,30);
      }
    }
  }
}

// delete onmouse events
function enl_noevents(enl_obj)
{
  enl_obj.onmousedown = null;
  enl_obj.onclick = null;
  enl_obj.onmouseup = null;
}

// add event to window.onload
function enl_addLoad(enl_func)
{
  var enl_oldonload = window.onload;
  if (typeof window.onload != 'undefined')
  { window.onload = enl_func; }
  else
  { window.onload = function() {
    if (enl_oldonload) { enl_oldonload(); }
    enl_func();
    };
  }
}

// show loader
function enl_ajaxload(enl_obj) {
  enl_ldr = enl_geto('enl_ldr');
  enl_r = enl_coord(enl_obj);
  enl_setpos(enl_ldr,enl_r.left+enl_r.width/2-17,enl_r.top+enl_r.height/2-17);
  enl_visible(enl_ldr);
}

// hide loader
function enl_ajaxldrhide() {
  enl_ldr = enl_geto('enl_ldr');
  enl_hide(enl_ldr);
  enl_setpos(enl_ldr,-5000,0);
}

// get next/previous pic (enl_prvnxt=0 gets next, =1 gets prev)
function enl_getnext(enl_imgid,enl_prvnxt)
{
  enl_oripic = enl_geto(enl_geto(enl_imgid).orig);
  if (enl_oripic.className)
  {
    var enl_allElm = document.body.getElementsByTagName('img');
    var enl_flag = 0;
    if (!enl_prvnxt)
    {
      for(var enl_i = 0; enl_i < enl_allElm.length; enl_i++)
      {
        if ((enl_flag == 1) && (enl_allElm[enl_i].className==enl_oripic.className) && !enl_allElm[enl_i].orig)
        {
          enl_flag = 2;
          enl_nextObj = enl_allElm[enl_i];
        }
        if (enl_oripic == enl_allElm[enl_i]) enl_flag = 1;
      }
    }
    else
    {
      for(var enl_i = enl_allElm.length; enl_i >= 0; enl_i--)
      {
        if ((enl_flag == 1) && (enl_allElm[enl_i].className==enl_oripic.className) && !enl_allElm[enl_i].orig)
        {
          enl_flag = 2;
          enl_nextObj = enl_allElm[enl_i];
        }
        if (enl_oripic == enl_allElm[enl_i]) enl_flag = 1;
      }
    }
    if (enl_flag == 2 && !enl_nextObj.isenlarged && enl_oripic.className != 'imgflowimg' && enl_oripic.className != 'sliderimg') return enl_nextObj;
    else return null;
  }
}

// draw border
function enl_mkborder(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_img.className = '';  // removes the OC image shadow
  enl_brdclone = enl_geto(enl_imgid+"brd");
  if (enl_wheelnav) enl_wheelenable(enl_brdclone);
  if (enl_titlebar && enl_brdsize < enl_btnheight+4)
  {
    enl_tmph = enl_img.newh + enl_brdsize + enl_btnheight + 4;
    enl_tmpt = enl_img.newt-enl_brdsize- (enl_btnheight+4) +enl_brdsize;
  }
  else
  {
    enl_tmph = enl_img.newh + enl_brdsize*2;
    enl_tmpt = enl_img.newt-enl_brdsize;
  }
  enl_setpos(enl_brdclone,enl_img.newl - enl_brdsize, enl_tmpt);
  with (enl_brdclone.style) {
    width = eval(enl_img.neww + enl_brdsize*2)+'px';
    height = enl_tmph+'px';
    visibility = 'visible';
    zIndex = enl_zcnt-1;
  }
  if (enl_shadow) enl_dropshadow(enl_imgid);
  if (typeof enl_hideselect != 'undefined') enl_hideselect(enl_brdclone,0);
}

// remove border
function enl_delborder(enl_imgid)
{
  enl_brdm=enl_geto(enl_imgid+"brd");
  if (typeof enl_hideselect != 'undefined') enl_hideselect(enl_brdclone,1);
  enl_hide(enl_brdm);
  enl_setpos(enl_brdm,-5000,0);
  if (enl_shadow) enl_delshadow(enl_imgid);
  enl_img.className = enl_img.saveClassName;  // restores the OC image shadow
}

// -------- main functions -------

// initiate pre-load
function enlarge(enl_img)
{
  if (!enl_firstcall) enl_init();
  if (enl_firstcall == 1 || enl_img.isenlarged) return false;
  if (enl_inprogress) 
  {
    setTimeout('enl_openthepic("'+enl_img.id+'")',99);
    return false;
  }
  if (enl_brdbck && !enl_brdbckpic.complete) return false;
  if (enl_titlebar && (!enl_butact.complete || !enl_butinact.complete)) return false;
  var enl_getlongdesc=enl_img.getAttribute('longdesc');
  if (enl_getlongdesc.slice(3,5) == '::' && typeof enl_checkflash == 'undefined') return false;
  enl_inprogress = 1;
  enl_img.isenlarged = 1;
  enl_ctlslid(0);
  enl_preloadit(enl_getlongdesc);
  enl_imgid = enl_img.getAttribute('id');
  enl_inmax = 0;
  setTimeout('enl_chckready("'+enl_imgid+'")' ,10);
}

// check if pre-load is ready and create clone
function enl_chckready(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_ldr = enl_geto("enl_ldr");
  var enl_getlongdesc = enl_img.getAttribute('longdesc');
  var enl_divtype = enl_getlongdesc.slice(0,5);
  if (enl_stopload)
  {
    enl_geto('enl_ldrgif').onclick = null;
    enl_geto('enl_ldrgif').title = "";
    enl_keepblack = 0;
    if (enl_dark) enl_nodark();
    enl_ajaxldrhide();
    enl_inprogress = 0;
    enl_img.isenlarged = 0;
    enl_ctlslid(1);
    enl_stopload = 0;
    return false;
  }
  var enl_ispreload = enl_prldimg[enl_prldcnt].complete;
  if ((enl_ispreload && enl_prldimg[enl_prldcnt].width) || (enl_ispreload && enl_divtype == 'swf::') || (enl_ispreload && enl_divtype == 'fl2::')|| (enl_ispreload && enl_divtype == 'flv::') || enl_divtype == 'dvx::' || enl_divtype == 'ifr::' || (enl_isie && (enl_divtype == 'swf::')) || (enl_isie && (enl_divtype == 'fl2::')) || (enl_isie && (enl_divtype == 'flv::')))
  {
    enl_zcnt+=3;
    enl_ajaxldrhide();
    if (enl_divtype == 'swf::' || enl_divtype == 'fl2::' || enl_divtype == 'flv::') {
      if (enl_checkflash()) enl_clone = enl_swfdiv(enl_img,enl_getlongdesc);
      else 
      { 
        alert (enl_noflash);
        enl_inprogress = 0;
        enl_img.isenlarged = 0;
        enl_ctlslid(1);
        return false; 
      }
    }
    else if (enl_divtype == 'dvx::') enl_clone = enl_dvxdiv(enl_img,enl_getlongdesc);
    else if (enl_divtype == 'ifr::') enl_clone = enl_ifrdiv(enl_img,enl_getlongdesc);
    else
    {
      enl_clone = enl_img.cloneNode(true);
      enl_setpos(enl_clone,-5000,0);
      with (enl_clone) {
        id = enl_img.id+"clone";
        style.visibility = 'hidden';
        style.position = 'absolute';
        style.borderWidth = '0px';
        style.outlineWidth = '0px';
        style.margin = '0px';
        style.padding = '0px';
      }
      document.body.appendChild(enl_clone);
    }
    enl_clone.orig = enl_img.id;

    // clone brddiv to brdclone
    enl_brddiv = enl_geto("enl_brd");
    enl_brdclone = enl_brddiv.cloneNode(true);
    enl_brdclone.id = enl_imgid + "clonebrd";
    enl_brdclone.style.zIndex = enl_zcnt-1;
    if (enl_shadow) {
      enl_shddiv = enl_geto("enl_shd");
      enl_shdclone = enl_shddiv.cloneNode(true);
      enl_shdclone.id = enl_clone.id+"shd1";
      enl_shdclone2 = enl_shddiv.cloneNode(true);
      enl_shdclone2.id = enl_clone.id+"shd2";
      document.body.appendChild(enl_shdclone);
      document.body.appendChild(enl_shdclone2);
    }
    document.body.appendChild(enl_brdclone);
    try { enl_img.blur(); } catch(enl_err) {}
    enl_clone.ispic = (enl_getlongdesc.slice(3,5) != '::') ? 1 : 0;
    setTimeout('enl_doenlarge("'+enl_clone.id+'")' ,50);
  } else {
     enl_ajaxload(enl_img);
     enl_geto('enl_ldrgif').onclick = function () { enl_stopload = 1; };
     enl_geto('enl_ldrgif').title = enl_canceltext;
     enl_visible(enl_ldr);
     try { enl_img.blur(); } catch(enl_err) {}
     setTimeout('enl_chckready("'+enl_imgid+'")' ,50);
  }
}

// initiate enlarging
function enl_doenlarge(enl_imgid)
{
  enl_zcnt+=3;
  enl_getbrwsxy();
  if (typeof enl_butact != 'undefined') enl_btnheight = parseInt(enl_butact.height);
  document.onselectstart = function () { return false; };
  enl_img = enl_geto(enl_imgid);
  if (enl_wheelnav) enl_wheelenable(enl_img);
  enl_orig = enl_geto(enl_img.orig);
  try { enl_orig.blur(); } catch(enl_err) {}
  enl_noevents(enl_img);
  enl_noevents(enl_orig);
  enl_fullimg = enl_orig.getAttribute('longdesc');
  enl_r = enl_coord(enl_orig);
  enl_img.style.zIndex = enl_zcnt;
  enl_img.oldt = enl_r.top;
  enl_img.oldl = enl_r.left;
  if (typeof cpgif_conf_reflection_p == 'number' && enl_geto(enl_img.orig).className == "imgflowimg") enl_img.oldh = parseInt(enl_r.height / (1+cpgif_conf_reflection_p));
  else enl_img.oldh = enl_r.height;
  enl_img.oldw = enl_r.width;
  enl_img.issmaller = 0;
  if (enl_img.oldw+enl_img.oldl > enl_brwsx-20) enl_img.oldl = enl_brwsx-enl_img.oldw-20;
  if (enl_img.ispic) {
    enl_img.neww = parseInt(enl_prldimg[enl_prldcnt].width);
    enl_img.newh = parseInt(enl_prldimg[enl_prldcnt].height);
  }
  else
  {
    enl_img.neww = eval(enl_fullimg.split('::')[2]);
    enl_img.newh = eval(enl_fullimg.split('::')[3]);
  }
  if (enl_img.neww > enl_brwsx-100) {
      enl_img.newh = Math.round(enl_img.newh * (enl_brwsx-100) / enl_img.neww);
      enl_img.neww = enl_brwsx-100;
      enl_img.issmaller = 1;
  }
  if (enl_img.newh > enl_brwsy-80)  {
      enl_img.neww = Math.round(enl_img.neww * (enl_brwsy-80) / enl_img.newh);
      enl_img.newh = enl_brwsy-80;
      enl_img.issmaller = 1;
  }
  enl_img.newl = Math.round(enl_img.oldl - (enl_img.neww-enl_img.oldw)/2);
  enl_img.newt = Math.round(enl_img.oldt - (enl_img.newh-enl_img.oldh)/2);
  if (!enl_center)
  {
    if (enl_img.newl < (50 + enl_scrollx)) enl_img.newl = 50+enl_scrollx;
    if (enl_img.newt < (40 + enl_scrolly)) enl_img.newt = 40+enl_scrolly;
    if (enl_img.newl+enl_img.neww > enl_brwsx+enl_scrollx-50) enl_img.newl = enl_brwsx+enl_scrollx-50-enl_img.neww;
    if (enl_img.newt+enl_img.newh > enl_brwsy+enl_scrolly-40) enl_img.newt = enl_brwsy+enl_scrolly-40-enl_img.newh;
  }
  else
  {
    enl_img.newl = Math.round(enl_brwsx/2+enl_scrollx-enl_img.neww/2);
    enl_img.newt = Math.round(enl_brwsy/2+enl_scrolly-enl_img.newh/2);
  }
  enl_img.steps = 1;
  enl_img.thumbpic = enl_img.src;
  if (enl_titlebar) enl_mktitlebar(enl_imgid);
  if (!enl_ani || !enl_img.ispic ) enl_donoani(enl_imgid);
  else
  {
    if (enl_dark)
    {
      enl_infront = enl_imgid;
      setTimeout('enl_darken()', enl_speed*4);
    }
    if (enl_ani==1) setTimeout('enl_dofadein("'+enl_imgid+'")' ,50);
    else setTimeout('enl_doglidein("'+enl_imgid+'")' ,50);
  }
}

// show pic without animation
function enl_donoani(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_setpos(enl_img,enl_img.newl,enl_img.newt,enl_img.neww,enl_img.newh);
  enl_img.src = enl_fullimg;
  enl_img.style.position = 'absolute';
  enl_visible(enl_img);
  enl_mkborder(enl_imgid);
  if (enl_titlebar) enl_showbtn(enl_imgid);
  enl_makedraggable(enl_imgid);
  if (enl_dark) enl_darken();
}

// re-enable object for enlarge, room up
function enl_enable(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_orig = enl_geto(enl_img.orig);
  var enl_makenull=enl_geto(enl_orig.id+'swfinner');
  var enl_getlongdesc = enl_orig.getAttribute('longdesc');
     if ((enl_getlongdesc.slice(0,5) != 'swf::') && enl_isie)
     {
       for (var enl_i in enl_makenull) 
       {  
         if (typeof enl_makenull[enl_i] == "function") enl_makenull[enl_i] = null;
       }
     }
  if (enl_titlebar) document.body.removeChild(enl_geto(enl_imgid+"btns"));
  document.body.removeChild(enl_geto(enl_imgid+"brd"));
  if (enl_shadow) {
    document.body.removeChild(enl_geto(enl_imgid+"shd1"));
    document.body.removeChild(enl_geto(enl_imgid+"shd2"));
  }
  enl_orig.isenlarged = 0;
  enl_orig.onclick = function() { enlarge(this); };
  enl_setcur(enl_orig,enl_pluscur,'pointer','hand');
  document.body.removeChild(enl_img);
  enl_ctlslid(1);
  enl_inprogress = 0;
  if (enl_inmax == 1) enlarge(enl_orig);
}

// hide pic without animation
function enl_noaniremove(enl_imgid)
{
  enl_hide(enl_geto(enl_imgid));
  setTimeout('enl_enable("'+enl_imgid+'")' ,10);
}

// initiate shrinking of pic
function enl_shrink(enl_imgid)
{
  if (enl_inprogress) 
  {
    setTimeout('enl_shrink("'+enl_imgid+'")',50);
    return false;
  }
  enl_inprogress = 1;
  enl_infront = '';
  enl_ctlslid(0);
  enl_img = enl_geto(enl_imgid);
  if (enl_img.issmaller == 1 && enl_isie) enl_img.style.msInterpolationMode = 'nearest-neighbor';
  enl_setcur(enl_img,'','pointer','hand');
  enl_noevents(enl_img);
  enl_orig = enl_geto(enl_img.orig);
  enl_fullimg = enl_orig.getAttribute('longdesc');
  enl_visible(enl_img);
  enl_delborder(enl_imgid);
  if (enl_titlebar) enl_hidebtn(enl_imgid);
  if (enl_dark) enl_nodark();
  enl_keepblack = 0;
  enl_r = enl_coord(enl_geto(enl_img.orig));
  enl_img.oldt = enl_r.top;
  enl_img.oldl = enl_r.left;
  if (enl_img.oldw+enl_img.oldl > enl_brwsx-20) enl_img.oldl = enl_brwsx-enl_img.oldw-20;
  if (!enl_ani || !enl_img.ispic) enl_noaniremove(enl_imgid);
  else if (enl_ani==1) setTimeout('enl_dofadeout("'+enl_imgid+'")' ,20);
  else setTimeout('enl_doglideout("'+enl_imgid+'")' ,20);
}

enl_addLoad(enl_init);

// paint shadow
function enl_dropshadow(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_shdclone1 = enl_geto(enl_imgid+"shd1");
  enl_shdclone2 = enl_geto(enl_imgid+"shd2");
  enl_tmpw = enl_img.neww + enl_shadowsize + enl_brdsize*2 + 2;
  if (enl_titlebar && enl_brdsize < enl_btnheight+4) 
  {
    enl_tmph = enl_img.newh + enl_shadowsize + enl_brdsize*2 + 6 + enl_btnheight - enl_brdsize;
    enl_tmpt = enl_img.newt - enl_brdsize - 1 - (enl_btnheight + 4) + enl_brdsize;
  }
  else
  {
    enl_tmph = enl_img.newh + enl_shadowsize + enl_brdsize*2 + 2;
    enl_tmpt = enl_img.newt - enl_brdsize - 1;
  }
  enl_setpos(enl_shdclone1, enl_img.newl - enl_brdsize - 1, enl_tmpt, enl_tmpw, enl_tmph);
  enl_shdclone1.style.zIndex = enl_zcnt-2;
  enl_visible(enl_shdclone1);
  enl_setpos(enl_shdclone2, enl_img.newl - enl_brdsize - 2, enl_tmpt - 1, enl_tmpw+2, enl_tmph+2);
  enl_shdclone2.style.zIndex = enl_zcnt-2;
  enl_visible(enl_shdclone2);
}

// hide shadow
function enl_delshadow(enl_imgid)
{
  enl_shd1 = enl_geto(enl_imgid+"shd1");
  enl_shd2 = enl_geto(enl_imgid+"shd2");
  enl_hide(enl_shd1);
  enl_setpos(enl_shd1,-5000,0);
  enl_hide(enl_shd2);
  enl_setpos(enl_shd2,-5000,0);
}

// factor for glide
function enl_calcfact(enl_facthelp)
{
  var enl_factor;
  if (enl_ani==3) enl_factor = ((-1*Math.cos(enl_facthelp-0.2))+0.98)*3.5;
  else if (enl_ani == 4) enl_factor = (Math.sin(1.5 * Math.PI + enl_facthelp * Math.PI) + 1)/2;
  else if (enl_ani == 5) enl_factor = Math.pow(enl_facthelp, Math.pow(2,2));
  else enl_factor = enl_facthelp;
  return enl_factor;
}

// glide out
function enl_doglideout(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_mvcnt = 0;
  enl_img.steps++;
  if (enl_img.steps >= enl_maxstep)
  {
    enl_visible(enl_geto(enl_img.orig));
    enl_hide(enl_img);
    enl_img.steps = 1;
    setTimeout('enl_enable("'+enl_imgid+'")',50);
  }
  else
  {
    var enl_factor = enl_calcfact((enl_maxstep-enl_img.steps)/enl_maxstep);
    enl_tmpw = Math.round(enl_factor * (enl_img.neww-enl_img.oldw) + enl_img.oldw);
    enl_tmph = Math.round(enl_factor * (enl_img.newh-enl_img.oldh) + enl_img.oldh);
    enl_tmpt = Math.round(enl_img.oldt+(enl_img.newt-enl_img.oldt)*enl_factor);
    enl_tmpl = Math.round(enl_img.oldl+(enl_img.newl-enl_img.oldl)*enl_factor);
    if (enl_tmpw < 0) enl_tmpw = 0;
    if (enl_tmph < 0) enl_tmph = 0;
    enl_setpos(enl_img,enl_tmpl,enl_tmpt,enl_tmpw,enl_tmph);
    if (enl_opaglide) enl_setopa(enl_img, Math.round((enl_maxstep-enl_img.steps)/enl_maxstep*100));
    setTimeout('enl_doglideout("'+enl_imgid+'")' ,enl_speed);
  }
}

// glide in pic
function enl_doglidein(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_img.steps++;
  if (enl_img.steps >= enl_maxstep)
  {
    enl_setpos(enl_img,enl_img.newl,enl_img.newt,enl_img.neww,enl_img.newh);
    enl_img.steps = 1;
    if (enl_opaglide) 
    {
      enl_setopa(enl_img,100);
      enl_img.style.filter = '';
    }
    setTimeout('enl_mkborder("'+enl_imgid+'")' ,enl_speed);
    // if (enl_dark) setTimeout('enl_darken()', enl_speed*4);
    // moved before function call to darken first
    setTimeout('enl_makedraggable("'+enl_imgid+'")' ,enl_speed*3);
    if (enl_titlebar) setTimeout('enl_showbtn("'+enl_imgid+'")' ,enl_speed*2);
  }
  else
  {
    if (enl_img.steps == 2) {
      enl_img.src = enl_fullimg;
      enl_img.style.position = 'absolute';
      if (enl_opaglide) enl_setopa(enl_img,0);
      enl_visible(enl_img);
      if (!enl_opaglide) enl_hide(enl_geto(enl_img.orig));
    }
    var enl_factor = enl_calcfact(enl_img.steps/enl_maxstep);
    enl_tmpw = Math.round(enl_factor * (enl_img.neww-enl_img.oldw) + enl_img.oldw);
    enl_tmph = Math.round(enl_factor * (enl_img.newh-enl_img.oldh) + enl_img.oldh);
    enl_tmpt = Math.round(enl_img.oldt+(enl_img.newt-enl_img.oldt)*enl_factor);
    enl_tmpl = Math.round(enl_img.oldl+(enl_img.newl-enl_img.oldl)*enl_factor);
    if (enl_tmpw < 0) enl_tmpw = 0;
    if (enl_tmph < 0) enl_tmph = 0;
    enl_setpos(enl_img,enl_tmpl,enl_tmpt,enl_tmpw,enl_tmph);
    if (enl_opaglide) enl_setopa(enl_img, Math.round(enl_img.steps/enl_maxstep*100));
    setTimeout('enl_doglidein("'+enl_imgid+'")' ,enl_speed);
  }
}

// fade out
function enl_dofadeout(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_mvcnt = 0;
  enl_img.steps++;
  if (enl_img.steps >= enl_maxstep)
  {
    enl_img.steps = 1;
    enl_hide(enl_img);
    setTimeout('enl_enable("'+enl_imgid+'")',50);
  }
  else 
  {
    enl_setopa(enl_img,(1-enl_img.steps/enl_maxstep)*100);
    setTimeout('enl_dofadeout("'+enl_imgid+'")' ,enl_speed);    
  }
}

// fade in pic
function enl_dofadein(enl_imgid)
{
  enl_brddiv = enl_geto(enl_imgid+"brd");
  enl_img = enl_geto(enl_imgid);
  enl_img.steps++;
  if (enl_img.steps==2)
  {
    enl_setpos(enl_img,enl_img.newl,enl_img.newt,enl_img.neww,enl_img.newh);
    enl_setopa(enl_img,0);
    enl_img.src = enl_fullimg;
    enl_img.style.position = 'absolute';
    enl_visible(enl_img);
  }
  if (enl_img.steps >= enl_maxstep)
  {
    enl_setopa(enl_img,100);
    enl_img.style.filter = '';
    enl_img.steps = 1;
    enl_mkborder(enl_imgid);
    if (enl_titlebar) enl_showbtn(enl_imgid);
    setTimeout('enl_makedraggable("'+enl_imgid+'")' ,30);
    // if (enl_dark) setTimeout('enl_darken()', 100);
    // moved before function call to darken first
  }
  else
  {
    enl_setopa(enl_img,enl_img.steps/enl_maxstep*100);
    setTimeout('enl_dofadein("'+enl_imgid+'")' ,enl_speed);
  }
}


// mouse moved while dragging
function enl_mousemv(enl_el)
{
  if (enl_drgmode && enl_drgdrop) {
    enl_tmpl = enl_nn6 ? enl_tx + enl_el.clientX - enl_x : enl_tx + event.clientX - enl_x;
    enl_tmpt = enl_nn6 ? enl_ty + enl_el.clientY - enl_y : enl_ty + event.clientY - enl_y;
    enl_setpos(enl_drgelem,enl_tmpl,enl_tmpt);
    if (enl_titlebar && enl_brdsize<enl_btnheight+4) enl_setpos(enl_geto(enl_drgelem.id+"brd"),enl_tmpl - enl_brdsize,enl_tmpt - (enl_btnheight+4));
    else enl_setpos(enl_geto(enl_drgelem.id+"brd"),enl_tmpl - enl_brdsize,enl_tmpt - enl_brdsize);
    if (enl_titlebar) enl_showbtn(enl_drgelem.id);
    enl_mvcnt++;
    if (enl_mvcnt > 3) enl_hasmvd = true;
    return false;
  }
}

// test for right button clicked
function enl_is_rightbutton(e)
{
  // http://stackoverflow.com/questions/2405771/is-right-click-a-javascript-event
  e = e || window.event;
  if ("which" in e)  // Gecko (Firefox), WebKit (Safari/Chrome) & Opera
    return e.which == 3;
  else if ("button" in e)  // IE, Opera
    return e.button == 2;
  else
    return false;
}

// start dragging
function enl_buttonpress(enl_el)
{
  if (enl_is_rightbutton(enl_el))
    return;
  enl_drgelem = enl_nn6 ? enl_el.target : event.srcElement; var topenl_el = enl_nn6 ? "HTML" : "BODY"; enl_hasmvd = false; while (enl_drgelem.tagName != topenl_el && !enl_drgelem.newh) {
  enl_drgelem = enl_nn6 ? enl_drgelem.parentNode : enl_drgelem.parentElement; } enl_drgmode = true; enl_zcnt+=3;
  var enl_drgid = enl_drgelem.id;
  if (enl_titlebar) enl_geto(enl_drgid+'btns').style.zIndex = enl_zcnt+1;
  enl_drgelem.style.zIndex = enl_zcnt; 
  if (enl_shadow) enl_delshadow(enl_drgid);
  enl_geto(enl_drgid+"brd").style.zIndex = enl_zcnt-1;
  enl_tx = parseInt(enl_drgelem.style.left+0); enl_ty = parseInt(enl_drgelem.style.top+0);
  enl_x = enl_nn6 ? enl_el.clientX : event.clientX; enl_y = enl_nn6 ? enl_el.clientY : event.clientY; enl_mvcnt = 0; enl_drgelem.onmousemove=enl_mousemv; return false;
}

// mouse btn released
function enl_enddrag(enl_el)
{
  if (enl_is_rightbutton(enl_el))
    return;   // ignore right button -- fixes http://redmine.opencaching.de/issues/947
  enl_noevents(enl_drgelem);
  enl_drgelem.newt = parseInt(enl_drgelem.style.top);
  enl_drgelem.newl = parseInt(enl_drgelem.style.left);
  var enl_drgid = enl_drgelem.id;
  if (enl_shadow) enl_dropshadow(enl_drgid);
  enl_drgmode = false;
  if (enl_hasmvd==true || !enl_drgelem.ispic) {
    if (typeof enl_hideselect != 'undefined') enl_hideselect(0,1);
    enl_mkborder(enl_drgid);
    if (enl_titlebar) enl_showbtn(enl_drgid);
    enl_hasmvd=false;
    setTimeout('enl_makedraggable("'+enl_drgid+'")' ,100);
  }
  else setTimeout('enl_shrink("'+enl_drgid+'")' ,10);
}

// create a button object
function enl_makebtn(enl_id,enl_offset)
{
  enl_tempbtn = document.createElement("a");
  enl_tempbtn.id = enl_id;
  enl_setcur(enl_tempbtn,'','pointer','hand');
  with (enl_tempbtn.style)
  {
    height = enl_btnheight+'px';
    width = enl_btnheight+'px';
    marginRight = '3px';
    backgroundImage = 'url('+enl_gifpath+enl_btninact+')';
    backgroundRepeat = 'no-repeat';
    backgroundPosition = enl_offset+'px 0px';
    display = 'block';
    styleFloat = 'left';
    cssFloat = 'left';
  }
  return enl_tempbtn;
}

// create title bar with buttons
function enl_mktitlebar(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_orig = enl_geto(enl_img.orig);
  enl_btns = enl_mkdiv(enl_imgid+'btns');
  enl_btns.style.padding = '2px';
  enl_maxwidth = parseInt(enl_img.neww)-enl_buttonurl.length*(enl_btnheight+3);
  if (enl_maxwidth > 100 && enl_orig.alt != '')
  {
    enl_title = document.createElement('div');
    with (enl_title.style)
    {
      position = 'relative';
      styleFloat = 'left';
      cssFloat = 'left';
      textAlign = 'center';
      paddingTop = '0px';
      fontFamily = 'Arial,Helvetica,sans-serif';
      fontSize = '10pt';
      color = (enl_titletxtcol) ? enl_titletxtcol : '#444444';
      whiteSpace = 'nowrap';
      fontWeight = 'bold';
    }
    enl_gettitle = enl_orig.alt;
    if (!enl_gettitle) enl_gettitle='';
    if (enl_gettitle.length > Math.round(enl_maxwidth*0.1)) enl_gettitle = enl_gettitle.substring(0, Math.round(enl_maxwidth*0.1)-2)+'...';
    enl_title.innerHTML = enl_gettitle;
    enl_title.style.width = enl_maxwidth+'px';
    enl_btns.appendChild(enl_title);
  }
  var enl_i = 0;
  // add buttons to title bar
  while (enl_i < enl_buttonurl.length) {
    if (enl_buttonurl[enl_i] == 'next' && enl_getnext(enl_imgid,0) == null) { enl_i++; continue; }
    else if (enl_buttonurl[enl_i] == 'prev' && enl_getnext(enl_imgid,1) == null) { enl_i++; continue; }
    else if (((enl_buttonurl[enl_i] == 'max') || (enl_buttonurl[enl_i] == 'maxpop')) && (enl_orig.getAttribute('longdesc').search(/normal_.+/) == -1)) { enl_i++; continue; }
    else if (!enl_clone.ispic && (enl_buttonurl[enl_i] == 'enl_bbcode.php?pos=-' || enl_buttonurl[enl_i] == 'enl_hist.php?pid=')) { enl_i++; continue; }
    enl_button[enl_i] = enl_makebtn(enl_imgid+enl_i,enl_buttonoff[enl_i]);
    enl_button[enl_i].title = enl_buttontxt[enl_i];
    enl_button[enl_i].whichpic = enl_imgid;
    enl_button[enl_i].ajaxurl = enl_buttonurl[enl_i];
    if (enl_buttonurl[enl_i].slice(0,5) == 'site:') enl_button[enl_i].onclick = function() { enl_gotosite(this); };
    else
    {
      switch (enl_buttonurl[enl_i]) {
        case 'close':
          enl_button[enl_i].onclick = function() { enl_shrink(enl_imgid); return false; };
          break;
        case 'max':
          enl_button[enl_i].onclick = function() { enl_max(enl_imgid); };
          break;
        case 'maxpop':
          var enl_maxmeurl = 'displayimage.php?pid='+enl_orig.name;
          enl_button[enl_i].onclick = function() { window.open(enl_maxmeurl+'&fullsize=1','Max','scrollbars=yes,toolbar=no,status=no,resizable=yes,width=900,height=650');this.blur;return false; };
          enl_button[enl_i].href = enl_maxmeurl+'&amp;fullsize=1';
          break;
        case 'pic':
          enl_button[enl_i].onclick = function() { enl_btnpicture(enl_imgid); };
          break;
        case 'prev':
          enl_button[enl_i].onclick = function() { enl_next(enl_imgid,1); };
          break;
        case 'next':
          enl_button[enl_i].onclick = function() { enl_next(enl_imgid,0); };
          break;
        default:
          if (typeof enl_ajax != 'undefined') enl_button[enl_i].onclick = function() { enl_btnajax(this); };
          break;
      }
    }
    enl_button[enl_i].onmouseover = function() { enl_btnmover(this); };
    enl_button[enl_i].onmouseout = function() { enl_btnmout(this); };
    enl_btns.appendChild(enl_button[enl_i]);
    enl_i++;
  }
  enl_img.btnw = enl_btns.offsetWidth;
}

// maximize pic
function enl_max(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_orig = enl_geto(enl_img.orig);
  enl_orig.setAttribute('longdesc',enl_orig.getAttribute('longdesc').replace(/normal_/, ''));
  enl_orig.setAttribute('longDesc',enl_orig.getAttribute('longdesc').replace(/normal_/, ''));
  enl_inmax = 1;
  setTimeout('enl_shrink("'+enl_imgid+'")' ,10);
}

//  next/prev button (0=next, 1=prev)
function enl_next(enl_imgid,enl_prvnxt)
{
  if (enl_infront != '')
  {
    enl_nextPic = enl_getnext(enl_imgid,enl_prvnxt);
    if (enl_nextPic)
    {
      if (enl_dark == 2) enl_keepblack = 1;
      enl_shrink(enl_imgid);
      setTimeout('enl_openthepic("'+enl_nextPic.id+'")', 50);
    }
  }
}

// pic button
function enl_btnpicture(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_geto(enl_imgid+'brd').innerHTML = '';
  enl_visible(enl_img);
  enl_ajaxldrhide();
}

// goto website
function enl_gotosite(enl_obj)
{
  enl_img = enl_geto(enl_obj.whichpic);
  enl_orig = enl_geto(enl_img.orig);
  enl_imgid = enl_img.id;
  enl_ajaxload(enl_img);
  enl_geturl = enl_obj.ajaxurl.slice(5);
  if (enl_orig.getAttribute('name')) enl_geturl += enl_orig.getAttribute('name');
  window.location = enl_geturl.replace(/pid=/, 'pos=-');
}

// mouse over for buttons
function enl_btnmover(enl_obj)
{
  enl_obj.style.backgroundImage = 'url('+enl_gifpath+enl_btnact+')';
}

// mouse out for buttons
function enl_btnmout(enl_obj)
{
  enl_obj.style.backgroundImage = 'url('+enl_gifpath+enl_btninact+')';
}

// show titlebar
function enl_showbtn(enl_imgid)
{
  enl_btns = enl_geto(enl_imgid+'btns');
  enl_img = enl_geto(enl_imgid);
  enl_tmpl = parseInt(enl_img.style.left)+enl_img.neww-enl_img.btnw+5;
  enl_tmpt = (enl_titlebar && enl_brdsize < enl_btnheight+4) ? parseInt(enl_img.style.top)-(enl_btnheight+4) : parseInt(enl_img.style.top)-enl_brdsize;
  enl_setpos(enl_btns,enl_tmpl,enl_tmpt);
  enl_btns.style.zIndex = enl_zcnt+1;
  enl_visible(enl_btns);
}

// hide titlebar
function enl_hidebtn(enl_imgid)
{
  enl_btns = enl_geto(enl_imgid+'btns');
  enl_hide(enl_btns);
  enl_setpos(enl_btns,-5000,0);
}

// AJAX button
function enl_btnajax(enl_obj)
{
  enl_img = enl_geto(enl_obj.whichpic);
  enl_orig = enl_geto(enl_img.orig);
  enl_imgid = enl_img.id;
  enl_geturl = enl_obj.ajaxurl;
  if (enl_orig.getAttribute('name')) enl_geturl += enl_orig.getAttribute('name');
  enl_geturl += (enl_geturl.indexOf("?") <0) ? "?enl_img="+enl_imgid : "&enl_img="+enl_imgid;
  enl_ajax (enl_img,enl_geturl);
}

// load AJAX content and show in border div
function enl_ajax(enl_img,enl_url)
{
  enl_brdm = enl_geto(enl_img.id+'brd');
  enl_brdm.innerHTML = '';
  enl_ajaxload(enl_brdm);
  enl_hide(enl_img);
  var enl_randomizenumber = Math.round(9999*Math.random());
  var enl_randomizeit = (enl_url.indexOf("?") <0) ? "?enl_rndit="+enl_randomizenumber : "&enl_rndit="+enl_randomizenumber;
  enl_geturl += (enl_geturl.indexOf("?") <0) ? "?enl_img="+enl_imgid : "&enl_img="+enl_imgid;

  try
  {
    enl_request.open('GET', enl_url+enl_randomizeit, true);
    enl_request.onreadystatechange = function()
    {
      if (enl_request.readyState == 4) {
        enl_ajaxldrhide();
        enl_answer = enl_request.responseText;
        enl_divh = enl_img.newh-2;
        enl_divw = enl_img.neww-2;
        var enl_myajaxcol = (enl_ajaxcolor) ? enl_ajaxcolor : '#d0d0d0';
        enl_tmphtml = '<div style="width:'+enl_divw+'px;height:'+enl_divh+'px;overflow:auto;border-color:#666677;border-width:1px;border-style:solid;background-color:'+enl_myajaxcol+';margin-left:'+enl_brdsize+'px;margin-bottom:'+enl_brdsize+'px;margin-right:'+enl_brdsize+'px;margin-top:';
        enl_tmphtml += (enl_brdsize < enl_btnheight+4) ? eval(enl_btnheight+4) : enl_brdsize;
        enl_tmphtml += 'px;">'+enl_answer+'</div>';
        enl_geto(enl_imgid+'brd').innerHTML = enl_tmphtml;
        }
    };
    enl_request.send(null);
  }
  catch(enl_err)
  {
    enl_ajaxldrhide();
    enl_geto(enl_imgid+'brd').innerHTML = "<center><br/><br/><p style='font-size:12px;'>AJAX did not work";
  }
}

// follow AJAX link
function enl_ajaxfollow(enl_obj)
{
  enl_link = enl_obj.name;
  enl_imgid = enl_link.split("enl_img=")[1];
  if (enl_imgid.indexOf("&")) enl_imgid = enl_imgid.split("&")[0];
  enl_img = enl_geto(enl_imgid);
  enl_ajax(enl_img,enl_link);
}

// prep AJAX request
function enl_ajaxprepare()
{
    enl_request = false;
    if (window.XMLHttpRequest) {
      enl_request = new XMLHttpRequest();
      if (enl_usecounter) enl_request2 = new XMLHttpRequest();
    }
    else if (enl_isie)
    {
      try { enl_request = new ActiveXObject("Msxml2.XMLHTTP");
            if (enl_usecounter) enl_request2 = new ActiveXObject("Msxml2.XMLHTTP");
         }
      catch (enl_err) { try { enl_request = new ActiveXObject("Microsoft.XMLHTTP"); 
                              if (enl_usecounter) enl_request2 = new ActiveXObject("Microsoft.XMLHTTP");
                            } catch (enl_err) {} }
    }
}

// increase counter
function enl_count(enl_imgid)
{
  enl_img = enl_geto(enl_imgid);
  enl_geturl = enl_counterurl;
  if (enl_img.getAttribute('name')) enl_geturl += enl_img.getAttribute('name');
  try {
    enl_request2.open('GET', enl_geturl, true);
    enl_request2.send(null);
  } catch(enl_err) {}
}

// darken background
function enl_darken(enl_flag)
{
  enl_drk = enl_geto('enl_drk');
  if (enl_darkened == 0)
  {
    enl_setopa(enl_drk,0);
    enl_darkened=1;
    enl_visible(enl_drk);
    enl_resize();
    if (enl_flag) enl_fadedark(enl_darksteps-1);
    else enl_fadedark(0);
  }
}

function enl_fadedark(enl_darkenstep)
{
  if (enl_infront == '') enl_nodark;
  else
    {
      enl_drk = enl_geto('enl_drk');
      enl_darkenstep++;
      enl_setopa(enl_drk,enl_darkprct/enl_darksteps*enl_darkenstep);
      if (enl_darkenstep < enl_darksteps) setTimeout('enl_fadedark('+enl_darkenstep+')' ,4);
    }
}

// end darkening
function enl_nodark()
{
  if (!enl_keepblack)
  {
    enl_drk = enl_geto('enl_drk');
    enl_hide(enl_drk);
    enl_setpos(enl_drk,-5000,0,0,0);
    enl_darkened=0;
  }
}

// add event on window resize
function enl_addResize(enl_resfunc)
{
  var enl_oldonresize = window.onresize;
  if (typeof window.onresize != 'function') window.onresize = enl_resfunc;
  else
  {
    window.onresize = function()
    {
      enl_resfunc();
      if (enl_oldonresize) { setTimeout('"+enl_oldonresize+"',25); }
    };
  }
}

// window resize
function enl_resize()
{
  if (enl_darkened)
  {
    enl_drk = enl_geto('enl_drk');
    enl_setpos(enl_drk,0,0,0,0);
    enl_getbrwsxy();
    if (window.innerHeight && window.scrollMaxY) enl_darkh = (window.innerHeight + window.scrollMaxY > enl_brwsy) ? window.innerHeight + window.scrollMaxY : enl_brwsy;
    else enl_darkh = (document.body.scrollHeight > document.body.offsetHeight) ? document.body.scrollHeight : document.body.offsetHeight;
    enl_darkh = (enl_brwsy > enl_darkh) ? enl_brwsy : enl_darkh;
    enl_setpos(enl_drk,0,0,document.body.scrollWidth,enl_darkh);
  }
}

// create darken div
function enl_darkenprepare()
{
  enl_drk = enl_mkdiv('enl_drk');
  enl_drk.style.backgroundColor = 'black';
  enl_drk.style.zIndex = 9670;
  enl_addResize(enl_resize);
  if (enl_wheelnav) enl_wheelenable(enl_drk);
}

// check if flash plugin is installed
function enl_checkflash()
{
  var enl_checkflash, enl_flashthere=0;
  try { enl_checkflash = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"); enl_flashthere = 1; } catch(enl_err){}
  try { enl_checkflash = navigator.plugins["Shockwave Flash"]; if(enl_checkflash) enl_flashthere = 1; } catch(enl_err) {}
  return enl_flashthere;
}

// create flash div as enl_clone
function enl_swfdiv(enl_img,enl_getlongdesc)
{
  enl_clone = enl_mkdiv(enl_img.id+'clone');
  enl_clone.style.overflow = 'hidden';
  if (enl_getlongdesc.slice(0,5) == 'swf::')
  {
    var enl_swfsrc = (enl_isie) ? ' id="' + enl_img.id+'swfinner" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="' + enl_getlongdesc.split('::')[1] + '"' : '';
    enl_swfsrc += ' width="' + enl_getlongdesc.split('::')[2] + '" height="' + enl_getlongdesc.split('::')[3] + '"><param name="movie" value="' + enl_getlongdesc.split('::')[1] + '"><param name="allowFullScreen" value="true"></param><param name="wmode" value="opaque"></param>';
    enl_swfsrc += '<embed id="' + enl_img.id+'swfinneremb' + '" src="' + enl_getlongdesc.split('::')[1] + '" type="application/x-shockwave-flash" width="' + enl_getlongdesc.split('::')[2] + '" allowFullScreen="true" wmode="opaque" height="' + enl_getlongdesc.split('::')[3] + '"></embed></object>';
  }
  else if (enl_getlongdesc.slice(0,5) == 'flv::')
  {
    var enl_swfsrc = (enl_isie)? ' id="' + enl_img.id+'swfinner" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="'+enl_gifpath+'flvPlayer.swf"' : '';
    enl_swfsrc += ' width="' + enl_getlongdesc.split('::')[2] + '" height="' + enl_getlongdesc.split('::')[3] + '"><param name="movie" value="'+enl_gifpath+'flvPlayer.swf"></param><param name="allowFullScreen" value="true"></param><param name="FlashVars" value="flvPath='+enl_getlongdesc.split('::')[1]+'&flvTitle=FLV Video"></param>';
    enl_swfsrc += '<embed id="' + enl_img.id+'swfinneremb' + '" src="'+enl_gifpath+'flvPlayer.swf" type="application/x-shockwave-flash" width="' + enl_getlongdesc.split('::')[2] + '" allowfullscreen="true" FlashVars="flvPath='+enl_getlongdesc.split('::')[1]+'&flvTitle=FLV Video" height="' + enl_getlongdesc.split('::')[3] + '"></embed></object>';
  }
  else
  {
    var enl_swfsrc = (enl_isie)? ' id="' + enl_img.id+'swfinner" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="'+enl_gifpath+'player.swf?movie='+enl_getlongdesc.split('::')[1]+'&autoload=on&fgcolor=0xFF0000&bgcolor=0x000000&volume=70&autorewind=0"' : '';
    enl_swfsrc += ' width="' + enl_getlongdesc.split('::')[2] + '" height="' + enl_getlongdesc.split('::')[3] + '"><param name="movie" value="'+enl_gifpath+'player.swf?movie='+enl_getlongdesc.split('::')[1]+'&autoload=on&fgcolor=0xFF0000&bgcolor=0x000000&volume=70&autorewind=0"></param><param name="allowFullScreen" value="true"></param>';
    enl_swfsrc += '<embed id="' + enl_img.id+'swfinneremb' + '" src="'+enl_gifpath+'player.swf?movie='+enl_getlongdesc.split('::')[1]+'&autoload=on&fgcolor=0xFF0000&bgcolor=0x000000&volume=70&autorewind=0" type="application/x-shockwave-flash" width="' + enl_getlongdesc.split('::')[2] + '" allowfullscreen="true" height="' + enl_getlongdesc.split('::')[3] + '"></embed></object>';
  }
  // internet explorer 
  if (enl_isie)
  {
    enl_swfinnerdiv = document.createElement("div");
    enl_swfinnerdiv.id = enl_img.id+'swfinner';
    enl_clone.appendChild(enl_swfinnerdiv);
    enl_swfinnerdiv.outerHTML = '<object style="margin:0px;" '+enl_swfsrc;
  }
  // other browsers
  else
  {
    enl_clone.innerHTML = '<div style="margin:0px;overflow:hidden;"><object '+enl_swfsrc+'</div>';
  }
  return enl_clone;
}

// create iframe div as enl_clone
function enl_ifrdiv(enl_img,enl_getlongdesc)
{
  enl_clone = enl_mkdiv(enl_img.id+'clone');
  enl_clone.style.overflow = 'hidden';
  enl_ifr = document.createElement('iframe');
  enl_ifr.src = enl_getlongdesc.split('::')[1];
  enl_ifr.style.margin = '0px';
  enl_ifr.style.width = enl_getlongdesc.split('::')[2]+'px';
  enl_ifr.style.height = enl_getlongdesc.split('::')[3]+'px';
  enl_ifr.style.border = 'none';
  enl_ifr.style.frameborder = '0';
  enl_clone.appendChild(enl_ifr);
  return enl_clone;
}

// create divx webplayer div as enl_clone
function enl_dvxdiv(enl_img,enl_getlongdesc)
{
  enl_clone = enl_mkdiv(enl_img.id+'clone');
  enl_clone.style.overflow = 'hidden';
  enl_dvxhtml = '<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" id="' + enl_img.id+'divxinner" width="'+enl_getlongdesc.split('::')[2]+'" height="'+enl_getlongdesc.split('::')[3]+'" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">';
  enl_dvxhtml += '<param name="src" value="'+enl_getlongdesc.split('::')[1]+'"/><param name="loop" value="true"/><param name="bannerEnabled" value="false"/><embed type="video/divx" src="'+enl_getlongdesc.split('::')[1]+'" width="'+enl_getlongdesc.split('::')[2]+'" loop="true" bannerEnabled="false" height="'+enl_getlongdesc.split('::')[3]+'" pluginspage="http://go.divx.com/plugin/download/"></embed></object>';
  enl_clone.innerHTML = enl_dvxhtml;
  return enl_clone;
}

// wheel navigation
function enl_wheel(enl_wheelevent)
{
  var enl_wheeldelta = 0;
  if (!enl_wheelevent) enl_wheelevent = window.event;
  if (enl_wheelevent.wheelDelta) enl_wheeldelta = enl_wheelevent.wheelDelta;
  else if (enl_wheelevent.detail) enl_wheeldelta = -enl_wheelevent.detail;
  if (enl_infront != '')
  {
    if (enl_wheeldelta > 0) enl_next(enl_infront,0);
    if (enl_wheeldelta < 0) enl_next(enl_infront,1);
  }
  if (enl_wheelevent.preventDefault) enl_wheelevent.preventDefault();
  enl_wheelevent.returnValue = false;
  return false;
}

function enl_wheelenable(enl_obj)
{
  if (window.addEventListener) enl_obj.addEventListener('DOMMouseScroll', enl_wheel, false);
  else enl_obj.onmousewheel = enl_wheel;
}

// key listening function
function enl_keynavi(enl_keyevent) {
  if (enl_infront != '')
  {
    enl_keyevent = enl_keyevent || window.event;
    enl_key = enl_keyevent.keyCode;
    switch (enl_key)
    {
      case 39:
        enl_next(enl_infront,0);
        break;
      case 37:
        enl_next(enl_infront,1);
        break;
      case 27:
        enl_shrink(enl_infront);
        break;
    }
  }
}

// hide select elements IE <=6
function enl_hideselect(enl_obj,enl_flag)
{
  if (enl_ie6)
  {
    if (!enl_flag)
    {
      enl_objpos = enl_coord(enl_obj);
      enl_objpos.bottom = enl_objpos.top + enl_objpos.height;
      enl_objpos.right = enl_objpos.left + enl_objpos.width;
    }
    enl_selectlist = document.getElementsByTagName('select');
    for (var enl_i=0; enl_i<enl_selectlist.length; enl_i++)
    {
      if (!enl_flag) {
        enl_selectpos = enl_coord(enl_selectlist[enl_i]);
        enl_selectpos.bottom = enl_selectpos.top + enl_selectpos.height;
        enl_selectpos.right = enl_selectpos.left + enl_selectpos.width;
        if ((enl_selectpos.top >= enl_objpos.top && enl_selectpos.top <= enl_objpos.bottom && enl_selectpos.left >= enl_objpos.left && enl_selectpos.left <= enl_objpos.right) || (enl_selectpos.bottom >= enl_objpos.top && enl_selectpos.bottom <= enl_objpos.bottom && enl_selectpos.right >= enl_objpos.left && enl_selectpos.right <= enl_objpos.right))
        { 
          enl_hide(enl_selectlist[enl_i]);
        }
      } 
      else 
      {
        enl_visible(enl_selectlist[enl_i]);
      }
    }
  }
}

/* ============================================================
   MADAME PATISSERIE & COFFEE — Ortak JavaScript
   ============================================================ */
(function () {
  'use strict';

  /* ---- LOADER ---- */
  function initLoader() {
    var loader = document.querySelector('.loader');
    if (!loader) return;
    document.body.classList.add('loading');
    window.addEventListener('load', function () {
      setTimeout(function () {
        loader.classList.add('hidden');
        document.body.classList.remove('loading');
      }, 2500);
    });
  }
  initLoader();

  /* ---- NAVBAR ---- */
  var nav = document.getElementById('nav');
  var navToggle = document.getElementById('navToggle');
  var navLinks = document.getElementById('navLinks');

  if (nav) {
    window.addEventListener('scroll', function () {
      if (window.pageYOffset > 80) {
        nav.classList.add('scrolled');
      } else {
        nav.classList.remove('scrolled');
      }
    }, { passive: true });
  }

  if (navToggle && navLinks) {
    navToggle.addEventListener('click', function () {
      navToggle.classList.toggle('active');
      navLinks.classList.toggle('open');
      document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
    });

    document.querySelectorAll('.nav__link').forEach(function (link) {
      link.addEventListener('click', function () {
        navToggle.classList.remove('active');
        navLinks.classList.remove('open');
        document.body.style.overflow = '';
      });
    });
  }

  /* ---- SCROLL REVEAL ---- */
  function initReveal() {
    var els = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
    if (!('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('visible'); });
      return;
    }
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -60px 0px', threshold: 0.1 });
    els.forEach(function (el) { obs.observe(el); });
  }
  initReveal();

  /* ---- COUNTER ANIMATION ---- */
  function initCounters() {
    var nums = document.querySelectorAll('.stat__number[data-count]');
    if (!nums.length) return;
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        var target = parseInt(el.getAttribute('data-count'));
        var suffix = el.getAttribute('data-suffix') || '';
        var prefix = el.getAttribute('data-prefix') || '';
        var duration = 1800;
        var start = null;
        function step(ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / duration, 1);
          var ease = 1 - Math.pow(1 - p, 3);
          el.textContent = prefix + Math.floor(ease * target) + suffix;
          if (p < 1) requestAnimationFrame(step);
          else el.textContent = prefix + target + suffix;
        }
        requestAnimationFrame(step);
        obs.unobserve(el);
      });
    }, { threshold: 0.5 });
    nums.forEach(function (el) { obs.observe(el); });
  }
  initCounters();

  /* ---- MENU CATEGORY FILTER ---- */
  function initMenuFilter() {
    var tabs = document.querySelectorAll('.category-tab');
    var items = document.querySelectorAll('[data-cat]');
    if (tabs.length && items.length) {
      tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          tabs.forEach(function (t) { t.classList.remove('active'); });
          tab.classList.add('active');
          var cat = tab.getAttribute('data-category');
          items.forEach(function (item) {
            if (cat === 'all' || item.getAttribute('data-cat') === cat) {
              item.style.display = '';
              item.classList.remove('visible');
              setTimeout(function () { item.classList.add('visible'); }, 50);
            } else {
              item.style.display = 'none';
            }
          });
        });
      });
    }

    // Homepage Menu Filter
    var homeTabs = document.querySelectorAll('.menu__cat-btn');
    var homeItems = document.querySelectorAll('.menu__item');
    if (homeTabs.length && homeItems.length) {
      homeTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          homeTabs.forEach(function (t) { t.classList.remove('active'); });
          tab.classList.add('active');
          var cat = tab.getAttribute('data-category');
          homeItems.forEach(function (item) {
            if (cat === 'all' || item.getAttribute('data-category') === cat) {
              item.style.display = '';
              item.classList.remove('visible');
              setTimeout(function () { item.classList.add('visible'); }, 50);
            } else {
              item.style.display = 'none';
            }
          });
        });
      });
    }
  }
  initMenuFilter();


  /* ---- LIGHTBOX ---- */
  function initLightbox() {
    var lightbox = document.getElementById('lightbox');
    if (!lightbox) return;
    var content = lightbox.querySelector('.lightbox__content');
    var closeBtn = lightbox.querySelector('.lightbox__close');

    document.querySelectorAll('[data-lightbox]').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var src = trigger.getAttribute('data-lightbox');
        var isVideo = src.match(/\.(mp4|webm|ogg)$/i);
        content.innerHTML = '';
        if (isVideo) {
          var vid = document.createElement('video');
          vid.src = src;
          vid.controls = true;
          vid.autoplay = true;
          vid.style.maxWidth = '90vw';
          vid.style.maxHeight = '85vh';
          vid.style.borderRadius = '20px';
          content.appendChild(vid);
        } else {
          var img = document.createElement('img');
          img.src = src;
          img.alt = 'Galeri görseli';
          content.appendChild(img);
        }
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    function closeLB() {
      lightbox.classList.remove('active');
      document.body.style.overflow = '';
      setTimeout(function () { content.innerHTML = ''; }, 400);
    }
    if (closeBtn) closeBtn.addEventListener('click', closeLB);
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox) closeLB();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && lightbox.classList.contains('active')) closeLB();
    });
  }
  initLightbox();

  /* ---- SMOOTH SCROLL ---- */
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = this.getAttribute('href');
      if (id === '#') return;
      var target = document.querySelector(id);
      if (target) {
        e.preventDefault();
        var off = (nav ? nav.offsetHeight : 0) + 20;
        window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - off, behavior: 'smooth' });
      }
    });
  });

  /* ---- PARALLAX HERO VIDEO ---- */
  var heroVideoWrapper = document.querySelector('.page-hero__video');
  if (heroVideoWrapper) {
    window.addEventListener('scroll', function () {
      var h = document.querySelector('.page-hero');
      if (h && window.pageYOffset < h.offsetHeight) {
        heroVideoWrapper.style.transform = 'translateY(' + (window.pageYOffset * 0.2) + 'px)';
      }
    }, { passive: true });
  }

  /* ---- 3D DECO MOUSE TILT ---- */
  function initDecoTilt() {
    var decos = document.querySelectorAll('.deco-3d--tilt');
    if (!decos.length) return;
    document.addEventListener('mousemove', function (e) {
      var cx = window.innerWidth / 2;
      var cy = window.innerHeight / 2;
      var dx = (e.clientX - cx) / cx;
      var dy = (e.clientY - cy) / cy;
      decos.forEach(function (d) {
        var intensity = parseFloat(d.getAttribute('data-tilt-intensity') || '15');
        d.style.transform = 'rotateY(' + (dx * intensity) + 'deg) rotateX(' + (-dy * intensity) + 'deg)';
      });
    });
  }
  initDecoTilt();

  /* ---- VIDEO PLAY/PAUSE TOGGLE ---- */
  document.querySelectorAll('.video-showcase__play').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var vid = btn.closest('.video-showcase').querySelector('video');
      if (!vid) return;
      if (vid.paused) { vid.play(); btn.style.opacity = '0'; }
      else { vid.pause(); btn.style.opacity = '1'; }
    });
  });

  document.querySelectorAll('.video-showcase').forEach(function (wrap) {
    var vid = wrap.querySelector('video');
    var btn = wrap.querySelector('.video-showcase__play');
    if (vid && btn) {
      vid.addEventListener('click', function () {
        if (!vid.paused) { vid.pause(); btn.style.opacity = '1'; }
      });
    }
  });

})();

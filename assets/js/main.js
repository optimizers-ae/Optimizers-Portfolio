(() => {
  'use strict';

  const menuButton = document.getElementById('menuToggle');
  const nav = document.getElementById('primaryNav');
  const header = document.getElementById('siteHeader');
  const heroImage = document.getElementById('heroImage');
  const servicesDropdown = document.getElementById('servicesDropdown');
  const servicesToggle = document.getElementById('servicesToggle');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const closeServices = () => {
    servicesDropdown?.classList.remove('is-open');
    servicesToggle?.setAttribute('aria-expanded', 'false');
    servicesDropdown?.querySelectorAll('.nav-dropdown__nested-group.is-open').forEach((group) => {
      group.classList.remove('is-open');
      group.querySelector('.nav-dropdown__parent')?.setAttribute('aria-expanded', 'false');
    });
  };

  servicesDropdown?.querySelectorAll('.nav-dropdown__parent').forEach((parentButton) => {
    parentButton.addEventListener('click', (event) => {
      if (window.matchMedia('(min-width: 901px)').matches) return;
      event.stopPropagation();
      const group = parentButton.closest('.nav-dropdown__nested-group');
      if (!group) return;
      const shouldOpen = !group.classList.contains('is-open');
      servicesDropdown.querySelectorAll('.nav-dropdown__nested-group.is-open').forEach((openGroup) => {
        if (openGroup === group) return;
        openGroup.classList.remove('is-open');
        openGroup.querySelector('.nav-dropdown__parent')?.setAttribute('aria-expanded', 'false');
      });
      group.classList.toggle('is-open', shouldOpen);
      parentButton.setAttribute('aria-expanded', String(shouldOpen));
    });
  });

  servicesToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    if (!servicesDropdown) return;

    const shouldOpen = !servicesDropdown.classList.contains('is-open');
    servicesDropdown.classList.toggle('is-open', shouldOpen);
    servicesToggle.setAttribute('aria-expanded', String(shouldOpen));
  });

  document.addEventListener('click', (event) => {
    if (servicesDropdown && !servicesDropdown.contains(event.target)) {
      closeServices();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeServices();
  });

  menuButton?.addEventListener('click', () => {
    const open = menuButton.classList.toggle('is-open');
    nav?.classList.toggle('is-open', open);
    menuButton.setAttribute('aria-expanded', String(open));
    if (!open) closeServices();
  });

  nav?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      nav.classList.remove('is-open');
      closeServices();
      menuButton?.classList.remove('is-open');
      menuButton?.setAttribute('aria-expanded', 'false');
    });
  });

  requestAnimationFrame(() => {
    requestAnimationFrame(() => document.body.classList.add('hero-ready'));
  });

  let ticking = false;

  const updateScrollEffects = () => {
    const scrollPosition = window.scrollY;
    header?.classList.toggle('is-scrolled', scrollPosition > 28);

    if (!reducedMotion && heroImage) {
      if (scrollPosition > 4) {
        const shift = Math.min(scrollPosition * 0.08, 44);
        heroImage.style.transform = `translate3d(0, ${shift}px, 0) scale(1.055)`;
      } else {
        heroImage.style.removeProperty('transform');
      }
    }

    ticking = false;
  };

  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(updateScrollEffects);
      ticking = true;
    }
  }, { passive: true });

  const revealItems = document.querySelectorAll('[data-reveal]');

  if (revealItems.length) {
    if (reducedMotion || !('IntersectionObserver' in window)) {
      revealItems.forEach((item) => item.classList.add('is-visible'));
    } else {
      const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        });
      }, {
        threshold: 0.16,
        rootMargin: '0px 0px -70px 0px'
      });

      revealItems.forEach((item) => revealObserver.observe(item));
    }
  }

  /* =====================================
     CLIENT LOGOS — ADD OR REMOVE ITEMS HERE
     Example:
     { src: 'assets/images/clients/client-name.webp', alt: 'Client Name' }
  ===================================== */
  const clients = [
    { src: 'assets/images/clients/client-logo-01.webp', alt: 'Optimizers client logo 01' },
    { src: 'assets/images/clients/client-logo-02.webp', alt: 'Optimizers client logo 02' },
    { src: 'assets/images/clients/client-logo-03.webp', alt: 'Optimizers client logo 03' },
    { src: 'assets/images/clients/client-logo-04.webp', alt: 'Optimizers client logo 04' },
    { src: 'assets/images/clients/client-logo-05.webp', alt: 'Optimizers client logo 05' },
    { src: 'assets/images/clients/client-logo-06.webp', alt: 'Optimizers client logo 06' },
    { src: 'assets/images/clients/client-logo-07.webp', alt: 'Optimizers client logo 07' },
    { src: 'assets/images/clients/client-logo-08.webp', alt: 'Optimizers client logo 08' },
    { src: 'assets/images/clients/client-logo-09.webp', alt: 'Optimizers client logo 09' },
    { src: 'assets/images/clients/client-logo-10.webp', alt: 'Optimizers client logo 10' },
    { src: 'assets/images/clients/client-logo-11.webp', alt: 'Optimizers client logo 11' },
    { src: 'assets/images/clients/client-logo-12.webp', alt: 'Optimizers client logo 12' },
    { src: 'assets/images/clients/client-logo-13.webp', alt: 'Optimizers client logo 13' },
    { src: 'assets/images/clients/client-logo-14.webp', alt: 'Optimizers client logo 14' },
    { src: 'assets/images/clients/client-logo-15.webp', alt: 'Optimizers client logo 15' },
    { src: 'assets/images/clients/client-logo-16.webp', alt: 'Optimizers client logo 16' },
    { src: 'assets/images/clients/client-logo-17.webp', alt: 'Optimizers client logo 17' },
    { src: 'assets/images/clients/client-logo-18.webp', alt: 'Optimizers client logo 18' },
    { src: 'assets/images/clients/client-logo-19.webp', alt: 'Optimizers client logo 19' },
    { src: 'assets/images/clients/client-logo-20.webp', alt: 'Optimizers client logo 20' },
    { src: 'assets/images/clients/client-logo-21.webp', alt: 'Optimizers client logo 21' },
    { src: 'assets/images/clients/client-logo-22.webp', alt: 'Optimizers client logo 22' },
    { src: 'assets/images/clients/client-logo-23.webp', alt: 'Optimizers client logo 23' },
    { src: 'assets/images/clients/client-logo-24.webp', alt: 'Optimizers client logo 24' }
  ];

  const clientsTrack = document.getElementById('clientsTrack');
  const clientsPrevious = document.getElementById('clientsPrev');
  const clientsNext = document.getElementById('clientsNext');
  const clientsProgress = document.getElementById('clientsProgress');

  if (clientsTrack) {
    const placeholderSvg = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 90">
        <rect x="1" y="1" width="238" height="88" rx="14" fill="#0a1117" stroke="#00B0D5" stroke-opacity=".45"/>
        <circle cx="91" cy="45" r="14" fill="none" stroke="#7D0414" stroke-width="5"/>
        <circle cx="91" cy="45" r="5" fill="#00B0D5"/>
        <text x="113" y="42" fill="#ffffff" font-family="Arial,sans-serif" font-size="12" font-weight="700">CLIENT</text>
        <text x="113" y="57" fill="#00B0D5" font-family="Arial,sans-serif" font-size="9" font-weight="700" letter-spacing="2">LOGO</text>
      </svg>`;
    const placeholderSrc = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(placeholderSvg)}`;

    clients.forEach((client) => {
      const card = document.createElement('article');
      const logoWrap = document.createElement('div');
      const image = document.createElement('img');

      card.className = 'op-client-card';
      logoWrap.className = 'op-client-card__logo';
      image.src = client.src || placeholderSrc;
      image.alt = client.alt;
      image.addEventListener('error', () => { image.src = placeholderSrc; }, { once: true });

      logoWrap.appendChild(image);
      card.appendChild(logoWrap);
      clientsTrack.appendChild(card);
    });

    let originalWidth = 0;
    let sliderPaused = false;
    let previousFrameTime = 0;
    let resumeTimer = 0;

    const sliderStep = () => {
      const card = clientsTrack.querySelector('.op-client-card');
      return card ? card.getBoundingClientRect().width + 12 : 220;
    };

    const updateClientsProgress = () => {
      if (!originalWidth) return;
      const ratio = Math.min(Math.max(clientsTrack.scrollLeft / originalWidth, 0), 1);
      const travel = Math.max((clientsProgress?.parentElement.clientWidth || 0) - (clientsProgress?.offsetWidth || 0), 0);
      if (clientsProgress) clientsProgress.style.transform = `translateX(${ratio * travel}px)`;
    };

    const normalizeClientsSlider = () => {
      if (!originalWidth) return;
      if (clientsTrack.scrollLeft >= originalWidth) clientsTrack.scrollLeft -= originalWidth;
    };

    const moveClientsSlider = (direction) => {
      sliderPaused = true;
      if (direction < 0 && clientsTrack.scrollLeft < sliderStep() * 1.5) clientsTrack.scrollLeft = originalWidth;
      clientsTrack.scrollLeft += direction * sliderStep() * 1.5;
      normalizeClientsSlider();
      updateClientsProgress();
      window.clearTimeout(resumeTimer);
      resumeTimer = window.setTimeout(() => { sliderPaused = false; }, 650);
    };

    const animateClientsSlider = (time) => {
      if (!previousFrameTime) previousFrameTime = time;
      const elapsed = Math.min(time - previousFrameTime, 40);
      previousFrameTime = time;

      if (!sliderPaused && !reducedMotion && !document.hidden) {
        clientsTrack.scrollLeft += elapsed * .038;
        normalizeClientsSlider();
        updateClientsProgress();
      }

      window.requestAnimationFrame(animateClientsSlider);
    };

    const originalCards = Array.from(clientsTrack.children);
    originalCards.forEach((card) => {
      const clone = card.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      clientsTrack.appendChild(clone);
    });

    originalWidth = clientsTrack.children[originalCards.length]?.offsetLeft || clientsTrack.scrollWidth / 2;

    clientsPrevious?.addEventListener('click', () => moveClientsSlider(-1));
    clientsNext?.addEventListener('click', () => moveClientsSlider(1));
    clientsTrack.addEventListener('scroll', () => { normalizeClientsSlider(); updateClientsProgress(); }, { passive: true });
    clientsTrack.addEventListener('mouseenter', () => { sliderPaused = true; });
    clientsTrack.addEventListener('mouseleave', () => { sliderPaused = false; });
    clientsTrack.addEventListener('focusin', () => { sliderPaused = true; });
    clientsTrack.addEventListener('focusout', () => { sliderPaused = false; });
    clientsTrack.addEventListener('touchstart', () => { sliderPaused = true; }, { passive: true });
    clientsTrack.addEventListener('touchend', () => { sliderPaused = false; }, { passive: true });
    window.addEventListener('resize', () => {
      originalWidth = clientsTrack.children[originalCards.length]?.offsetLeft || clientsTrack.scrollWidth / 2;
      normalizeClientsSlider();
    }, { passive: true });

    updateClientsProgress();
    window.requestAnimationFrame(animateClientsSlider);
  }

  updateScrollEffects();
})();
    (() => {
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const revealItems = document.querySelectorAll('[data-reveal]');

      if (reducedMotion || !('IntersectionObserver' in window)) {
        revealItems.forEach((item) => item.classList.add('is-visible'));
      } else {
        const observer = new IntersectionObserver((entries, watch) => {
          entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            watch.unobserve(entry.target);
          });
        }, { threshold: .12, rootMargin: '0px 0px -55px' });
        revealItems.forEach((item) => observer.observe(item));
      }

      document.querySelectorAll('.op-industry').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
          const bounds = card.getBoundingClientRect();
          card.style.setProperty('--spot-x', `${event.clientX - bounds.left}px`);
          card.style.setProperty('--spot-y', `${event.clientY - bounds.top}px`);
        });
      });
    })();
    (() => {
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const revealItems = document.querySelectorAll('[data-reveal]');
      const timeline = document.getElementById('processTimeline');

      if (reducedMotion || !('IntersectionObserver' in window)) {
        revealItems.forEach((item) => item.classList.add('is-visible'));
        timeline?.style.setProperty('--line-progress','1');
      } else {
        const observer = new IntersectionObserver((entries, watch) => {
          entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            watch.unobserve(entry.target);
          });
        }, { threshold: .13, rootMargin: '0px 0px -55px' });
        revealItems.forEach((item) => observer.observe(item));

        if (timeline) {
          const timelineObserver = new IntersectionObserver((entries, watch) => {
            if (!entries[0].isIntersecting) return;
            timeline.style.setProperty('--line-progress','1');
            watch.disconnect();
          }, { threshold: .25 });
          timelineObserver.observe(timeline);
        }
      }

      document.querySelectorAll('.op-process__step').forEach((card) => {
        card.addEventListener('pointermove',(event) => {
          const bounds = card.getBoundingClientRect();
          card.style.setProperty('--spot-x',`${event.clientX - bounds.left}px`);
          card.style.setProperty('--spot-y',`${event.clientY - bounds.top}px`);
        });
      });
    })();
    (() => {
      const brandIcons = {
        'YouTube': 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
        'Meta': 'M6.915 4.03c-1.968 0-3.683 1.28-4.871 3.113C.704 9.208 0 11.883 0 14.449c0 3.309 1.227 5.521 4.439 5.521 2.352 0 3.65-1.65 6.628-6.764l.942-1.664 2.335 3.895c1.77 2.956 3.24 4.533 5.53 4.533 2.78 0 4.126-2.006 4.126-5.56 0-5.286-2.663-10.38-6.8-10.38-2.006 0-3.75 1.54-5.083 3.358C10.38 5.18 8.72 4.03 6.915 4.03zm10.16 2.053c2.747 0 4.639 4.235 4.639 8.4 0 1.548-.368 2.9-1.839 2.9-1.225 0-2.156-1.563-4.496-5.363l-.617-1.028c.99-1.53 1.63-2.522 2.314-3.24.7-.735 1.32-1.67 0-1.67zm-10.201.553c1.521 0 2.588 1.209 3.909 3.025l-1.02 1.566c-2.404 3.695-3.73 6.155-5.323 6.155-1.132 0-1.847-1.031-1.847-2.84 0-4.078 1.808-7.906 4.281-7.906z',
        'Shopify': 'M15.337 23.979l7.216-1.561s-2.604-17.613-2.625-17.73c-.018-.116-.114-.192-.211-.192s-1.929-.136-1.929-.136-1.275-1.274-1.439-1.411l-1.415 21.03h.403zM11.71 11.305s-.81-.424-1.774-.424c-1.447 0-1.504.906-1.504 1.141 0 1.232 3.24 1.715 3.24 4.629 0 2.295-1.44 3.76-3.406 3.76-2.354 0-3.54-1.465-3.54-1.465l.646-2.086s1.245 1.066 2.28 1.066c.675 0 .975-.545.975-.932 0-1.619-2.654-1.694-2.654-4.359-.034-2.237 1.571-4.416 4.827-4.416 1.257 0 1.875.361 1.875.361l-.945 2.725zM11.17.83c-3.011 0-4.495 3.877-4.96 5.016l-2.16.674c-.675.213-.694.232-.772.87L1.448 21.453 15.009 24l.927-21.166-.792.231C14.721 1.832 13.968.695 12.636.695h-.115C12.135.209 11.669 0 11.265 0',
        'WordPress': 'M12 0C5.385 0 0 5.385 0 12s5.385 12 12 12 12-5.385 12-12S18.615 0 12 0zm0 1.215c2.809 0 5.365 1.072 7.286 2.833-1.151-.049-1.953.914-1.953 1.905 0 .89.513 1.643 1.06 2.531.411.72.89 1.643.89 2.977 0 .915-.354 1.994-.821 3.479l-1.075 3.585-3.9-11.61c.647-.03 1.232-.105 1.232-.105.582-.075.514-.93-.067-.899 0 0-1.755.135-2.88.135-1.064 0-2.85-.15-2.85-.15-.585-.03-.661.855-.075.885l1.125.09 1.68 4.605-2.37 7.08L5.354 6.9l1.234-.1c.585-.075.516-.93-.065-.896 0 0-1.746.138-2.874.138-.2 0-.438-.008-.69-.015A10.77 10.77 0 0 1 12 1.215zm-10.789 10.8c0-1.564.336-3.05.935-4.39L7.29 21.724a10.79 10.79 0 0 1-6.079-9.709zm10.789 10.77c-1.059 0-2.081-.153-3.048-.437l3.237-9.406 3.315 9.087A10.75 10.75 0 0 1 12 22.785zm5.424-1.46 3.295-9.527c.615-1.54.82-2.771.82-3.864 0-.405-.026-.78-.07-1.11A10.74 10.74 0 0 1 22.787 12c0 3.979-2.156 7.456-5.363 9.325z',
        'HighLevel': 'M3 20h18v2H3zm2-4 4-5 4 3 6-8 2 2-8 10-4-3-2 3zm0-9h3v3H5zm6-3h3v5h-3zm6-2h3v4h-3z',
        'Google': 'M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z',
        'LinkedIn': 'M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.11 1 2.5 1s2.48 1.12 2.48 2.5zM.34 8h4.32v14H.34V8zm7.02 0h4.14v1.91h.06c.58-1.09 1.99-2.24 4.09-2.24C20.02 7.67 21 10.55 21 14.3V22h-4.32v-6.83c0-1.63-.03-3.72-2.27-3.72-2.27 0-2.62 1.77-2.62 3.6V22H7.36V8z',
        'Semrush': 'M20.698 11.911c0 .444-.226.516-.79.516-.596 0-.706-.1-.77-.554-.118-1.152-.896-2.13-2.201-2.24-.418-.034-.518-.19-.518-.706 0-.48.074-.708.446-.708 2.265.01 3.833 1.832 3.833 3.69zm3.3 0c0-3.456-2.338-7.11-7.74-7.11H5.52c-.218 0-.354.11-.354.31 0 .109.082.209.156.26 1.26 1.01 3.615 1.888 3.615 2.232 0 .19-.136.308-.4.308H.372c-.254 0-.372.164-.372.326 0 .716 3.832 3.407 8.095 5.468.236.11.308.236.308.372-.008.154-.126.28-.4.28H4.1c-.216 0-.344.12-.344.3 0 .75 5.295 4.721 12.377 4.721 5.465 0 7.867-4.087 7.867-7.289zm-7.133 5.104c-2.794 0-5.132-2.276-5.132-5.114 0-2.794 2.33-5.04 5.132-5.04 2.863 0 5.111 2.24 5.111 5.04a5.086 5.086 0 0 1-5.111 5.114z',
        'TikTok Ads': 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-1.15.75-1.75 2.12-1.5 3.36.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.29-.5.4-.83.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z',
        'Amazon Ads': 'M3 17.5c4.7 3.4 11.6 3.6 17.6.2l1.2 1.6c-6.8 4.4-15 4.1-20.2-.1L3 17.5zm17.1-1.4c1.5-.2 4.9-.6 5.5.5.5 1-.6 4.8-1.1 6.1l-1.4-.6c.4-1.1 1.2-3.7.8-4.2-.4-.5-2.7-.3-3.8-.2v-1.6zM8.3 8.2c.2-3.2 2.8-4.5 5.7-4.5 3.2 0 5.6 1.2 5.6 4.8v6.2c0 1 .4 1.5 1 2l-2.6 2.2c-.7-.7-1.2-1.4-1.5-2.2-1.4 1.5-3.1 2.4-5.3 2.4-3 0-4.9-1.8-4.9-4.6 0-2.3 1.3-3.9 3.4-4.7 1.8-.7 4.3-.8 6.3-1v-.5c0-1.7-.7-2.4-2.2-2.4-1.4 0-2.2.7-2.5 2.2l-3-.1zm7.7 3c-2.3.1-5.9.4-5.9 2.8 0 1.2.8 2 2.1 2 1 0 2-.6 2.7-1.5.8-1.1 1.1-2.1 1.1-3.3z',
        'Ubersuggest': 'M4 3v10c0 5.1 3.1 8 8 8s8-2.9 8-8V3h-4v9.8c0 3-1.2 4.4-4 4.4s-4-1.4-4-4.4V3H4zm12.8 14.8 5 5 1.8-1.8-5-5-1.8 1.8z'
      };

      document.querySelectorAll('.op-tech__card').forEach((card) => {
        const name = card.querySelector('.op-tech__name')?.textContent.trim();
        const symbol = card.querySelector('.op-tech__symbol');
        if (!name || !symbol || symbol.querySelector('img') || !brandIcons[name]) return;
        symbol.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="${brandIcons[name]}"></path></svg>`;
      });

      const items = document.querySelectorAll('[data-reveal]');
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      if (reducedMotion || !('IntersectionObserver' in window)) {
        items.forEach((item) => item.classList.add('is-visible'));
        return;
      }

      const observer = new IntersectionObserver((entries, watch) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          watch.unobserve(entry.target);
        });
      }, { threshold: .14, rootMargin: '0px 0px -50px' });

      items.forEach((item) => observer.observe(item));
    })();

    (() => {
      const questions = document.querySelectorAll('.op-faq-item__question');
      if (!questions.length) return;

      questions.forEach((button) => {
        button.addEventListener('click', () => {
          const item = button.closest('.op-faq-item');
          const willOpen = !item?.classList.contains('is-open');

          document.querySelectorAll('.op-faq-item').forEach((other) => {
            other.classList.remove('is-open');
            other.querySelector('.op-faq-item__question')?.setAttribute('aria-expanded', 'false');
          });

          if (willOpen && item) {
            item.classList.add('is-open');
            button.setAttribute('aria-expanded', 'true');
          }
        });
      });
    })();

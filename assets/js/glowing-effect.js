/**
 * Multi-Node Optical Cursor Glow Effect
 * Physics-driven chained spring cursor illumination & optical wake
 * Lightweight, GPU-accelerated Vanilla JS (Zero dependencies)
 */
(() => {
  'use strict';

  const TRAIL_COUNT = 9;

  const GLOW_PRESETS = {
    cyan: {
      name: 'Electric Cyan',
      sheen: 'rgba(0, 176, 213, 0.08)',
      headCore: 'radial-gradient(circle, rgba(230, 252, 255, 0.95) 0%, rgba(0, 176, 213, 0.7) 40%, rgba(14, 165, 233, 0.25) 70%, transparent 85%)',
      headSparkleBg: 'rgba(224, 247, 255, 0.95)',
      sparkleShadow: '0 0 16px rgba(186, 230, 253, 0.95), 0 0 30px rgba(0, 176, 213, 0.65)',
      trailGradients: [
        'radial-gradient(circle, rgba(224,247,255,0.92) 0%, rgba(0,176,213,0.7) 35%, rgba(14,165,233,0.35) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(186,230,253,0.88) 0%, rgba(14,165,233,0.65) 35%, rgba(59,130,246,0.3) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(125,211,252,0.82) 0%, rgba(2,132,199,0.55) 35%, rgba(37,99,235,0.25) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(96,165,250,0.75) 0%, rgba(59,130,246,0.5) 35%, rgba(244,63,94,0.25) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(251,113,133,0.7) 0%, rgba(244,63,94,0.45) 35%, rgba(239,68,68,0.22) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(248,113,113,0.6) 0%, rgba(239,68,68,0.4) 35%, rgba(220,38,38,0.18) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(239,68,68,0.5) 0%, rgba(220,38,38,0.35) 35%, transparent 70%)',
        'radial-gradient(circle, rgba(220,38,38,0.4) 0%, rgba(185,28,28,0.25) 35%, transparent 70%)',
        'radial-gradient(circle, rgba(185,28,28,0.32) 0%, rgba(153,27,27,0.18) 40%, transparent 70%)',
      ],
    },
    violet: {
      name: 'Ultraviolet',
      sheen: 'rgba(168, 85, 247, 0.08)',
      headCore: 'radial-gradient(circle, rgba(250, 235, 255, 0.95) 0%, rgba(217, 70, 239, 0.65) 40%, rgba(147, 51, 234, 0.25) 70%, transparent 85%)',
      headSparkleBg: 'rgba(250, 232, 255, 0.95)',
      sparkleShadow: '0 0 16px rgba(245, 208, 254, 0.95), 0 0 30px rgba(217, 70, 239, 0.65)',
      trailGradients: [
        'radial-gradient(circle, rgba(250,232,255,0.92) 0%, rgba(217,70,239,0.7) 35%, rgba(147,51,234,0.35) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(245,208,254,0.88) 0%, rgba(192,38,211,0.65) 35%, rgba(126,34,206,0.3) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(232,121,249,0.82) 0%, rgba(162,28,175,0.55) 35%, rgba(107,33,168,0.25) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(216,180,254,0.75) 0%, rgba(168,85,247,0.5) 35%, rgba(88,28,135,0.2) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(192,132,252,0.7) 0%, rgba(147,51,234,0.45) 35%, rgba(76,29,149,0.18) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(168,85,247,0.6) 0%, rgba(126,34,206,0.35) 35%, transparent 75%)',
        'radial-gradient(circle, rgba(147,51,234,0.5) 0%, rgba(107,33,168,0.3) 35%, transparent 70%)',
        'radial-gradient(circle, rgba(126,34,206,0.4) 0%, transparent 70%)',
        'radial-gradient(circle, rgba(107,33,168,0.3) 0%, transparent 70%)',
      ],
    },
    emerald: {
      name: 'Aurora Emerald',
      sheen: 'rgba(20, 184, 166, 0.08)',
      headCore: 'radial-gradient(circle, rgba(230, 255, 245, 0.95) 0%, rgba(52, 211, 153, 0.65) 40%, rgba(20, 184, 166, 0.25) 70%, transparent 85%)',
      headSparkleBg: 'rgba(236, 253, 245, 0.95)',
      sparkleShadow: '0 0 16px rgba(167, 243, 208, 0.95), 0 0 30px rgba(52, 211, 153, 0.65)',
      trailGradients: [
        'radial-gradient(circle, rgba(236,253,245,0.92) 0%, rgba(52,211,153,0.7) 35%, rgba(16,185,129,0.35) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(167,243,208,0.88) 0%, rgba(45,212,191,0.65) 35%, rgba(13,148,136,0.3) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(110,231,183,0.82) 0%, rgba(20,184,166,0.55) 35%, rgba(15,118,110,0.25) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(52,211,153,0.75) 0%, rgba(14,165,233,0.5) 35%, rgba(3,105,161,0.2) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(45,212,191,0.7) 0%, rgba(2,132,199,0.45) 35%, transparent 75%)',
        'radial-gradient(circle, rgba(20,184,166,0.6) 0%, rgba(14,116,144,0.35) 35%, transparent 75%)',
        'radial-gradient(circle, rgba(13,148,136,0.5) 0%, transparent 70%)',
        'radial-gradient(circle, rgba(15,118,110,0.4) 0%, transparent 70%)',
        'radial-gradient(circle, rgba(17,94,89,0.3) 0%, transparent 70%)',
      ],
    },
    starlight: {
      name: 'Pure Starlight',
      sheen: 'rgba(255, 255, 255, 0.08)',
      headCore: 'radial-gradient(circle, rgba(255, 255, 255, 0.98) 0%, rgba(224, 242, 254, 0.7) 40%, rgba(147, 197, 253, 0.25) 70%, transparent 85%)',
      headSparkleBg: '#ffffff',
      sparkleShadow: '0 0 16px rgba(255, 255, 255, 0.98), 0 0 30px rgba(186, 230, 253, 0.8)',
      trailGradients: [
        'radial-gradient(circle, rgba(255,255,255,0.95) 0%, rgba(224,242,254,0.7) 35%, rgba(186,230,253,0.35) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(240,249,255,0.9) 0%, rgba(186,230,253,0.65) 35%, rgba(147,197,253,0.3) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(224,242,254,0.85) 0%, rgba(147,197,253,0.55) 35%, rgba(96,165,250,0.25) 55%, transparent 75%)',
        'radial-gradient(circle, rgba(186,230,253,0.75) 0%, rgba(96,165,250,0.5) 35%, transparent 75%)',
        'radial-gradient(circle, rgba(147,197,253,0.7) 0%, rgba(59,130,246,0.45) 35%, transparent 75%)',
        'radial-gradient(circle, rgba(96,165,250,0.6) 0%, transparent 75%)',
        'radial-gradient(circle, rgba(59,130,246,0.5) 0%, transparent 70%)',
        'radial-gradient(circle, rgba(37,99,235,0.4) 0%, transparent 70%)',
        'radial-gradient(circle, rgba(29,78,216,0.3) 0%, transparent 70%)',
      ],
    },
  };

  const BASE_RATIOS = [
    { sizeRatio: 1.0, blurRatio: 0.12, opacity: 0.9 },
    { sizeRatio: 0.84, blurRatio: 0.11, opacity: 0.85 },
    { sizeRatio: 0.69, blurRatio: 0.09, opacity: 0.8 },
    { sizeRatio: 0.57, blurRatio: 0.08, opacity: 0.72 },
    { sizeRatio: 0.46, blurRatio: 0.06, opacity: 0.65 },
    { sizeRatio: 0.36, blurRatio: 0.05, opacity: 0.55 },
    { sizeRatio: 0.28, blurRatio: 0.04, opacity: 0.45 },
    { sizeRatio: 0.21, blurRatio: 0.03, opacity: 0.35 },
    { sizeRatio: 0.15, blurRatio: 0.025, opacity: 0.22 },
  ];

  /**
   * Initialize glowing effect on a target container element
   */
  function initGlowingEffect(targetElement, options = {}) {
    if (!targetElement) return null;

    // Accessibility check: Disable on devices without fine pointer or with reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isTouchOnly = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
    if (prefersReducedMotion || isTouchOnly) {
      return null;
    }

    const presetKey = options.preset || targetElement.dataset.glowPreset || 'cyan';
    const activePreset = GLOW_PRESETS[presetKey] || GLOW_PRESETS.cyan;
    const size = Number(options.size || targetElement.dataset.glowSize) || 280;
    const intensity = Number(options.intensity || targetElement.dataset.glowIntensity) || 1;
    const blur = Number(options.blur || targetElement.dataset.glowBlur) || 1;

    // Compute trail nodes geometry
    const trailNodes = BASE_RATIOS.map((item, idx) => {
      const nodeSize = Math.round(size * item.sizeRatio);
      const nodeBlur = Math.max(2, Math.round(size * item.blurRatio * blur));
      const nodeOpacity = Math.min(1, Math.max(0, item.opacity * intensity));
      return {
        size: nodeSize,
        blur: nodeBlur,
        opacity: nodeOpacity,
        bg: activePreset.trailGradients[idx] || activePreset.trailGradients[0],
      };
    });

    const coreSize = Math.round(size * 0.42);
    const coreBlur = Math.max(2, Math.round(size * 0.05 * blur));
    const sparkleSize = Math.max(12, Math.round(size * 0.08));

    // Create glow layer container
    let glowLayer = targetElement.querySelector('.op-hero__glow-layer');
    if (!glowLayer) {
      glowLayer = document.createElement('div');
      glowLayer.className = 'op-hero__glow-layer';
      glowLayer.setAttribute('aria-hidden', 'true');
      
      // Place right after media overlay to maintain perfect stacking
      const overlay = targetElement.querySelector('.op-hero__overlay');
      if (overlay && overlay.nextSibling) {
        targetElement.insertBefore(glowLayer, overlay.nextSibling);
      } else {
        targetElement.appendChild(glowLayer);
      }
    } else {
      glowLayer.innerHTML = '';
    }

    // Build node DOM elements
    const nodeElements = [];
    trailNodes.forEach((cfg, idx) => {
      const node = document.createElement('div');
      node.className = 'op-glow-node';
      node.style.width = `${cfg.size}px`;
      node.style.height = `${cfg.size}px`;
      node.style.background = cfg.bg;
      node.style.filter = `blur(${cfg.blur}px)`;
      node.style.opacity = cfg.opacity;
      glowLayer.appendChild(node);
      nodeElements.push(node);
    });

    // Core Highlight
    const headCore = document.createElement('div');
    headCore.className = 'op-glow-core';
    headCore.style.width = `${coreSize}px`;
    headCore.style.height = `${coreSize}px`;
    headCore.style.background = activePreset.headCore;
    headCore.style.filter = `blur(${coreBlur}px)`;
    headCore.style.opacity = String(intensity);
    glowLayer.appendChild(headCore);

    // Starlight Sparkle
    const headSparkle = document.createElement('div');
    headSparkle.className = 'op-glow-sparkle';
    headSparkle.style.width = `${sparkleSize}px`;
    headSparkle.style.height = `${sparkleSize}px`;
    headSparkle.style.background = activePreset.headSparkleBg;
    headSparkle.style.boxShadow = activePreset.sparkleShadow;
    glowLayer.appendChild(headSparkle);

    let targetX = targetElement.clientWidth / 2 || window.innerWidth / 2;
    let targetY = targetElement.clientHeight / 2 || window.innerHeight / 2;

    const points = Array.from({ length: TRAIL_COUNT }, () => ({
      x: targetX,
      y: targetY,
    }));

    let targetOpacity = 0;
    let currentOpacity = 0;
    let isInside = false;
    let isIntersecting = true;
    let animationFrame = null;
    let targetRect = targetElement.getBoundingClientRect();

    const updateRect = () => {
      targetRect = targetElement.getBoundingClientRect();
    };

    const animate = () => {
      if (!isIntersecting) {
        animationFrame = null;
        return;
      }

      // Physics interpolation for head node
      points[0].x += (targetX - points[0].x) * 0.22;
      points[0].y += (targetY - points[0].y) * 0.22;

      // Chained physics interpolation for trail nodes
      for (let i = 1; i < TRAIL_COUNT; i++) {
        const factor = Math.max(0.12, 0.26 - i * 0.016);
        points[i].x += (points[i - 1].x - points[i].x) * factor;
        points[i].y += (points[i - 1].y - points[i].y) * factor;
      }

      // Opacity interpolation
      currentOpacity += (targetOpacity - currentOpacity) * 0.08;
      glowLayer.style.opacity = currentOpacity.toFixed(3);

      // Hardware accelerated translate3d updates
      for (let i = 0; i < TRAIL_COUNT; i++) {
        const node = nodeElements[i];
        if (node) {
          const cfg = trailNodes[i];
          const halfSize = cfg.size * 0.5;
          node.style.transform = `translate3d(${(points[i].x - halfSize).toFixed(1)}px, ${(points[i].y - halfSize).toFixed(1)}px, 0)`;
        }
      }

      // Position core and sparkle
      const halfCore = coreSize * 0.5;
      headCore.style.transform = `translate3d(${(points[0].x - halfCore).toFixed(1)}px, ${(points[0].y - halfCore).toFixed(1)}px, 0)`;

      const halfSparkle = sparkleSize * 0.5;
      headSparkle.style.transform = `translate3d(${(points[0].x - halfSparkle).toFixed(1)}px, ${(points[0].y - halfSparkle).toFixed(1)}px, 0)`;

      // Sleep rAF when completely faded out to preserve 100% CPU/GPU when idle
      if (!isInside && currentOpacity < 0.005) {
        currentOpacity = 0;
        glowLayer.style.opacity = '0';
        animationFrame = null;
        return;
      }

      animationFrame = requestAnimationFrame(animate);
    };

    const startAnimation = () => {
      if (isIntersecting && !animationFrame) {
        animationFrame = requestAnimationFrame(animate);
      }
    };

    const handleMouseEnter = () => {
      isInside = true;
      targetOpacity = 1;
      updateRect();
      startAnimation();
    };

    const handleMouseMove = (e) => {
      targetX = e.clientX - targetRect.left;
      targetY = e.clientY - targetRect.top;

      const width = targetRect.width || 1;
      const height = targetRect.height || 1;
      const percentX = (targetX / width - 0.5) * 2;
      const percentY = (targetY / height - 0.5) * 2;

      targetElement.style.setProperty('--mouse-x', `${targetX.toFixed(1)}px`);
      targetElement.style.setProperty('--mouse-y', `${targetY.toFixed(1)}px`);
      targetElement.style.setProperty('--tilt-x', `${(-percentY * 5).toFixed(2)}deg`);
      targetElement.style.setProperty('--tilt-y', `${(percentX * 5).toFixed(2)}deg`);
      targetElement.style.setProperty('--sheen-x', `${((targetX / width) * 100).toFixed(1)}%`);
      targetElement.style.setProperty('--sheen-y', `${((targetY / height) * 100).toFixed(1)}%`);

      if (!isInside) {
        isInside = true;
        targetOpacity = 1;
        startAnimation();
      }
    };

    const handleMouseLeave = () => {
      isInside = false;
      targetOpacity = 0;
      targetElement.style.setProperty('--tilt-x', '0deg');
      targetElement.style.setProperty('--tilt-y', '0deg');
    };

    // IntersectionObserver to pause loop when scrolled off viewport (saves battery and 0 idle CPU)
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          isIntersecting = entry.isIntersecting;
          if (isIntersecting && isInside) {
            startAnimation();
          } else if (!isIntersecting && animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
          }
        });
      },
      { threshold: 0.05 }
    );

    observer.observe(targetElement);
    targetElement.addEventListener('mouseenter', handleMouseEnter, { passive: true });
    targetElement.addEventListener('mousemove', handleMouseMove, { passive: true });
    targetElement.addEventListener('mouseleave', handleMouseLeave, { passive: true });
    window.addEventListener('resize', updateRect, { passive: true });

    return {
      destroy: () => {
        observer.disconnect();
        targetElement.removeEventListener('mouseenter', handleMouseEnter);
        targetElement.removeEventListener('mousemove', handleMouseMove);
        targetElement.removeEventListener('mouseleave', handleMouseLeave);
        window.removeEventListener('resize', updateRect);
        if (animationFrame) {
          cancelAnimationFrame(animationFrame);
        }
        glowLayer.remove();
      },
      setPreset: (newPreset) => {
        if (!GLOW_PRESETS[newPreset]) return;
        targetElement.dataset.glowPreset = newPreset;
        initGlowingEffect(targetElement, { ...options, preset: newPreset });
      }
    };
  }

  // Auto-init on DOMContentLoaded or immediate if already ready
  const autoInit = () => {
    const hero = document.querySelector('.op-hero');
    if (hero) {
      initGlowingEffect(hero, { preset: 'cyan', size: 280, intensity: 1, blur: 1 });
    }

    // Also support any element with [data-glow-effect]
    document.querySelectorAll('[data-glow-effect]').forEach((el) => {
      if (el !== hero) {
        initGlowingEffect(el);
      }
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInit);
  } else {
    autoInit();
  }

  window.initGlowingEffect = initGlowingEffect;
  window.GLOW_PRESETS = GLOW_PRESETS;
})();

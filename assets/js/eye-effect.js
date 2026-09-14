/**
 * Interactive Mouse-Following Eyes Effect
 * Ultra-responsive trigonometric cursor tracking (zero lag, 120fps ready)
 */
(function initMouseFollowingEyes() {
  'use strict';

  function setup() {
    var eyeLeft = document.getElementById('opEyeLeft');
    var eyeRight = document.getElementById('opEyeRight');

    if (!eyeLeft || !eyeRight) return;

    var pupilLeft = eyeLeft.querySelector('.op-eye__pupil');
    var pupilRight = eyeRight.querySelector('.op-eye__pupil');

    if (!pupilLeft || !pupilRight) return;

    var leftCenter = { x: 0, y: 0, left: 0, right: 0, top: 0, bottom: 0 };
    var rightCenter = { x: 0, y: 0, left: 0, right: 0, top: 0, bottom: 0 };
    var maxMove = 20;

    function recalibrate() {
      var rLeft = eyeLeft.getBoundingClientRect();
      var rRight = eyeRight.getBoundingClientRect();

      leftCenter.x = rLeft.left + rLeft.width / 2;
      leftCenter.y = rLeft.top + rLeft.height / 2;
      leftCenter.left = rLeft.left;
      leftCenter.right = rLeft.right;
      leftCenter.top = rLeft.top;
      leftCenter.bottom = rLeft.bottom;

      rightCenter.x = rRight.left + rRight.width / 2;
      rightCenter.y = rRight.top + rRight.height / 2;
      rightCenter.left = rRight.left;
      rightCenter.right = rRight.right;
      rightCenter.top = rRight.top;
      rightCenter.bottom = rRight.bottom;
    }

    function isInside(centerObj, x, y) {
      return x >= centerObj.left && x <= centerObj.right && y >= centerObj.top && y <= centerObj.bottom;
    }

    function updatePupils(mouseX, mouseY) {
      // If cursor is directly inside either eyeball, keep pupils from clipping
      if (isInside(leftCenter, mouseX, mouseY) || isInside(rightCenter, mouseX, mouseY)) {
        return;
      }

      // Left eye
      var dxL = mouseX - leftCenter.x;
      var dyL = mouseY - leftCenter.y;
      var angleL = Math.atan2(dyL, dxL);
      var pupilXL = Math.cos(angleL) * maxMove;
      var pupilYL = Math.sin(angleL) * maxMove;
      pupilLeft.style.transform = 'translate3d(' + pupilXL.toFixed(2) + 'px, ' + pupilYL.toFixed(2) + 'px, 0)';

      // Right eye
      var dxR = mouseX - rightCenter.x;
      var dyR = mouseY - rightCenter.y;
      var angleR = Math.atan2(dyR, dxR);
      var pupilXR = Math.cos(angleR) * maxMove;
      var pupilYR = Math.sin(angleR) * maxMove;
      pupilRight.style.transform = 'translate3d(' + pupilXR.toFixed(2) + 'px, ' + pupilYR.toFixed(2) + 'px, 0)';
    }

    var latestX = window.innerWidth / 2;
    var latestY = window.innerHeight / 2;
    var rafPending = false;

    function onPointerMove(e) {
      latestX = e.clientX;
      latestY = e.clientY;

      if (!rafPending) {
        rafPending = true;
        requestAnimationFrame(function() {
          updatePupils(latestX, latestY);
          rafPending = false;
        });
      }
    }

    window.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('mousemove', onPointerMove, { passive: true });
    window.addEventListener('touchmove', function(e) {
      if (e.touches && e.touches.length > 0) {
        latestX = e.touches[0].clientX;
        latestY = e.touches[0].clientY;
        if (!rafPending) {
          rafPending = true;
          requestAnimationFrame(function() {
            updatePupils(latestX, latestY);
            rafPending = false;
          });
        }
      }
    }, { passive: true });

    window.addEventListener('resize', recalibrate, { passive: true });
    window.addEventListener('scroll', recalibrate, { passive: true });

    // Initial calculation
    recalibrate();
    updatePupils(latestX, latestY);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setup);
  } else {
    setup();
  }
})();

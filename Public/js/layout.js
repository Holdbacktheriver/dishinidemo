  (function (doc, win) {
                var docEl = doc.documentElement,
                        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                        recalc = function () {
                            var clientWidth = docEl.clientWidth;
                            if (!clientWidth)
                                return;
                            if (clientWidth < 750 || clientWidth == 750) {
                                docEl.style.fontSize = 1 * (clientWidth / 15) + 'px';
                            } else {
                                docEl.style.fontSize = 50 + 'px';
                            }
                        };
                if (!doc.addEventListener)
                    return;
                win.addEventListener(resizeEvt, recalc, false);
                doc.addEventListener('DOMContentLoaded', recalc, false);
            })(document, window);
            function shield() {
                return false;
            }
            document.addEventListener('touchstart', shield, false);
/* ════════════════════════════════════════════════════════════════
   Enhancements & Animations - MyFishTank
   Modern animations, gradient charts, and glassmorphism effects
   ════════════════════════════════════════════════════════════════ */

(function() {
    'use strict';

    /* ──────────────────────────────────────────────────────────────
       GAUGE ANIMATIONS & ENHANCEMENTS
       ────────────────────────────────────────────────────────────── */

    /**
     * Animates gauge containers with pulse effect and float animation
     */
    function initGaugeAnimations() {
        const gaugeContainers = document.querySelectorAll('.chart-container, [id^="chart_"]');
        gaugeContainers.forEach(gauge => {
            // Add glass effect
            gauge.classList.add('glass-card', 'glass-pulse');
            gauge.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
    }

    /**
     * Adds gradient canvas for chart backgrounds
     */
    function enhanceChartGradients() {
        const charts = document.querySelectorAll('canvas[id*="line"], canvas[id*="area"]');
        charts.forEach(canvas => {
            if (canvas.dataset.gradientEnhanced) return;
            canvas.dataset.gradientEnhanced = 'true';
            canvas.style.borderRadius = '12px';
            canvas.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.3)';
        });
    }

    /**
     * Adds loading animation to data containers
     */
    function addLoadingPulse() {
        const dataCards = document.querySelectorAll('.card, .data-container');
        dataCards.forEach(card => {
            if (!card.dataset.animationActive) {
                card.dataset.animationActive = 'true';
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            }
        });
    }

    /**
     * Animates data values when they change
     */
    function animateValueUpdates() {
        // Target elements that display sensor values
        const valueElements = document.querySelectorAll('[data-sensor-value]');
        valueElements.forEach(element => {
            const observer = new MutationObserver(function(mutations) {
                element.style.animation = 'none';
                setTimeout(() => {
                    element.style.animation = 'fade-in 0.3s ease-out';
                }, 10);
            });

            observer.observe(element, { characterData: true, subtree: true });
        });
    }

    /**
     * Creates gradient effect for table rows
     */
    function styleTableRows() {
        const tables = document.querySelectorAll('table tbody tr');
        tables.forEach((row, index) => {
            row.style.transition = 'all 0.2s ease';
            row.addEventListener('mouseenter', function() {
                this.style.background = 'var(--glass-bg-medium)';
                this.style.borderLeft = '3px solid var(--primary-cyan)';
                this.style.paddingLeft = '0.75rem';
            });
            row.addEventListener('mouseleave', function() {
                if (index % 2 === 0) {
                    this.style.background = '';
                }
                this.style.borderLeft = 'none';
                this.style.paddingLeft = '1rem';
            });
        });
    }

    /**
     * Enhances form inputs with focus animations
     */
    function enhanceFormInputs() {
        const inputs = document.querySelectorAll('input.form-control, select.form-select, textarea.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.classList.add('glass-pulse');
                this.style.background = 'var(--glass-bg-medium)';
            });
            input.addEventListener('blur', function() {
                this.classList.remove('glass-pulse');
                this.style.background = 'var(--glass-bg-light)';
            });
        });
    }

    /**
     * Adds smooth scroll behavior to navigation
     */
    function smoothScrollNav() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Creates dynamic gauge ring effect with SVG
     * This enhances the existing gauge displays
     */
    function createAnimatedGaugeRings() {
        const gaugeElements = document.querySelectorAll('[id^="chart_"]');
        gaugeElements.forEach((elem, index) => {
            // Add visual enhancement classes
            elem.classList.add('fade-in');
            elem.style.animationDelay = `${index * 0.1}s`;

            // Create a wrapper for the glow effect
            const wrapper = document.createElement('div');
            wrapper.className = 'gauge-wrapper';
            wrapper.style.cssText = `
                position: relative;
                display: inline-block;
                width: 100%;
                border-radius: 12px;
            `;

            elem.parentNode.insertBefore(wrapper, elem);
            wrapper.appendChild(elem);
        });
    }

    /**
     * Adds status indicator badges for sensor data
     */
    function addSensorStatusIndicators() {
        const sensorCards = document.querySelectorAll('[data-sensor], .sensor-card');
        sensorCards.forEach(card => {
            const statusBadge = document.createElement('span');
            statusBadge.className = 'badge badge-success';
            statusBadge.innerHTML = '<i class="mdi mdi-circle-medium"></i> Online';
            statusBadge.style.cssText = `
                position: absolute;
                top: 1rem;
                right: 1rem;
                animation: float 3s ease-in-out infinite;
            `;

            if (card.classList.contains('card')) {
                card.style.position = 'relative';
                card.appendChild(statusBadge);
            }
        });
    }

    /**
     * Enhances buttons with ripple effect
     */
    function addButtonRippleEffect() {
        const buttons = document.querySelectorAll('button, a.btn, input[type="button"], input[type="submit"]');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    width: 50px;
                    height: 50px;
                    animation: ripple-effect 0.6s ease-out;
                    pointer-events: none;
                `;

                // Calculate position
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left - 25;
                const y = e.clientY - rect.top - 25;
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });
    }

    /**
     * Add ripple animation keyframe if not exists
     */
    function addRippleStyleSheet() {
        if (!document.querySelector('style[data-ripple]')) {
            const style = document.createElement('style');
            style.setAttribute('data-ripple', 'true');
            style.innerHTML = `
                @keyframes ripple-effect {
                    0% {
                        transform: scale(0);
                        opacity: 1;
                    }
                    100% {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Initializes all enhancements when DOM is ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runInitialization);
        } else {
            runInitialization();
        }
    }

    function runInitialization() {
        console.log('[Glassmorphism] Initializing enhancements...');

        initGaugeAnimations();
        enhanceChartGradients();
        addLoadingPulse();
        animateValueUpdates();
        styleTableRows();
        enhanceFormInputs();
        smoothScrollNav();
        createAnimatedGaugeRings();
        addSensorStatusIndicators();
        addButtonRippleEffect();
        addRippleStyleSheet();

        console.log('[Glassmorphism] Enhancements loaded successfully!');
    }

    // Auto-initialize
    init();

    // Expose functions to window for manual use if needed
    window.GlassmorphismEnhancements = {
        init: init,
        initGaugeAnimations: initGaugeAnimations,
        enhanceChartGradients: enhanceChartGradients,
        animateValueUpdates: animateValueUpdates
    };

})();

const BASELINE_EMISSION = 8.5;
const TAB_IDS = ['product', 'impact'];

export const productModal = () => ({
    selectedProduct: null,
    modalOpen: false,
    activeTab: 'product',
    lastFocusedElement: null,
    closeTimer: null,
    previousBodyPadding: '',
    scrollLocked: false,

    openProduct(product) {
        clearTimeout(this.closeTimer);
        this.closeTimer = null;
        this.lastFocusedElement = document.activeElement;
        this.selectedProduct = product;
        this.activeTab = 'product';
        this.modalOpen = true;

        this.lockScroll();

        this.$nextTick(() => {
            if (this.$refs.scrollArea) {
                this.$refs.scrollArea.scrollTop = 0;
            }

            this.$refs.closeButton?.focus();
        });
    },

    selectTab(tab) {
        if (this.activeTab === tab) {
            return;
        }

        this.activeTab = tab;

        this.$nextTick(() => {
            const scrollArea = this.$refs.scrollArea;
            const anchor = this.$refs.tabAnchor;

            if (!scrollArea || !anchor) {
                return;
            }

            if (scrollArea.scrollTop > anchor.offsetTop) {
                scrollArea.scrollTo({
                    top: anchor.offsetTop,
                    behavior: this.prefersReducedMotion ? 'auto' : 'smooth',
                });
            }
        });
    },

    handleTabKeydown(event) {
        const currentIndex = TAB_IDS.indexOf(this.activeTab);
        let nextIndex = null;

        if (event.key === 'ArrowRight') {
            nextIndex = (currentIndex + 1) % TAB_IDS.length;
        } else if (event.key === 'ArrowLeft') {
            nextIndex = (currentIndex - 1 + TAB_IDS.length) % TAB_IDS.length;
        } else if (event.key === 'Home') {
            nextIndex = 0;
        } else if (event.key === 'End') {
            nextIndex = TAB_IDS.length - 1;
        }

        if (nextIndex === null) {
            return;
        }

        event.preventDefault();

        const nextTab = TAB_IDS[nextIndex];

        this.selectTab(nextTab);

        this.$nextTick(() => {
            document.getElementById(`tab-btn-${nextTab}`)?.focus();
        });
    },

    closeModal(restoreFocus = true) {
        if (!this.modalOpen) {
            return;
        }

        this.modalOpen = false;

        clearTimeout(this.closeTimer);

        if (!restoreFocus) {
            this.unlockScroll();
        }

        this.closeTimer = setTimeout(() => {
            this.unlockScroll();

            if (restoreFocus) {
                this.lastFocusedElement?.focus();
            }
        }, 200);
    },

    trapFocus(event) {
        const dialog = this.$refs.dialog;

        if (!dialog) {
            return;
        }

        const focusable = Array.from(
            dialog.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
            ),
        ).filter((element) => element.offsetParent !== null);

        if (focusable.length === 0) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    },

    lockScroll() {
        if (this.scrollLocked) {
            return;
        }

        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;

        this.previousBodyPadding = document.body.style.paddingRight;
        document.body.style.overflow = 'hidden';

        if (scrollbarWidth > 0) {
            document.body.style.paddingRight = `${scrollbarWidth}px`;
        }

        this.scrollLocked = true;
    },

    unlockScroll() {
        if (!this.scrollLocked) {
            return;
        }

        document.body.style.overflow = '';
        document.body.style.paddingRight = this.previousBodyPadding;
        this.scrollLocked = false;
    },

    isAvailable(value) {
        if (value === null || value === undefined) {
            return false;
        }

        const normalized = String(value).trim().toLowerCase();

        return normalized !== '' && !normalized.includes('belum tersedia') && !normalized.includes('belum dinilai');
    },

    displayValue(value) {
        return this.isAvailable(value) ? value : 'Belum tersedia';
    },

    useInCalculator() {
        if (!this.selectedProduct) {
            return;
        }

        const { emission } = this.selectedProduct;

        this.$dispatch('calculate-product', { emission });
        this.closeModal(false);

        this.$nextTick(() => {
            document.getElementById('kalkulator')?.scrollIntoView({ behavior: 'smooth' });
        });

        setTimeout(() => {
            document.getElementById('calc-quantity')?.focus({ preventScroll: true });
        }, 220);
    },

    get baselineEmission() {
        return BASELINE_EMISSION;
    },

    get prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    },

    get savingsPercent() {
        if (!this.selectedProduct) {
            return 0;
        }

        return Math.round(((BASELINE_EMISSION - Number(this.selectedProduct.emission)) / BASELINE_EMISSION) * 100);
    },

    get savingsLabel() {
        const percent = this.savingsPercent;

        return percent >= 0 ? `−${percent}%` : `+${Math.abs(percent)}%`;
    },

    get savingsCaption() {
        return this.savingsPercent >= 0 ? 'lebih rendah' : 'lebih tinggi';
    },

    get comparisonMax() {
        return Math.max(BASELINE_EMISSION, Number(this.selectedProduct?.emission ?? 0));
    },

    get emissionBarWidth() {
        if (this.comparisonMax <= 0) {
            return 0;
        }

        return Math.round((Number(this.selectedProduct?.emission ?? 0) / this.comparisonMax) * 100);
    },

    get baselineBarWidth() {
        if (this.comparisonMax <= 0) {
            return 0;
        }

        return Math.round((BASELINE_EMISSION / this.comparisonMax) * 100);
    },
});

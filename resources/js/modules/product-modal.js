export const productModal = () => ({
    selectedProduct: null,
    modalOpen: false,
    activeTab: 'overview',
    tabs: [
        { id: 'overview', label: 'Ringkasan & Produsen', icon: 'fa-circle-info' },
        { id: 'process', label: 'Proses Produksi', icon: 'fa-gears' },
        { id: 'resources', label: 'Sumber Daya & Energi', icon: 'fa-faucet-drip' },
        { id: 'impact', label: 'Dampak Lingkungan', icon: 'fa-leaf' },
    ],
    lastFocusedElement: null,
    closeTimer: null,

    init() {
        this.$watch('modalOpen', (open) => {
            document.body.style.overflow = open ? 'hidden' : '';
        });
    },

    openProduct(product) {
        clearTimeout(this.closeTimer);
        this.closeTimer = null;
        this.lastFocusedElement = document.activeElement;
        this.selectedProduct = product;
        this.activeTab = 'overview';
        this.modalOpen = true;

        this.$nextTick(() => {
            this.$refs.closeButton?.focus();
        });
    },

    closeModal() {
        this.modalOpen = false;

        clearTimeout(this.closeTimer);

        this.closeTimer = setTimeout(() => {
            this.lastFocusedElement?.focus();
        }, 200);
    },

    useInCalculator() {
        if (!this.selectedProduct) {
            return;
        }

        const { emission } = this.selectedProduct;

        this.$dispatch('calculate-product', { emission });
        this.closeModal();

        document.getElementById('kalkulator')?.scrollIntoView({ behavior: 'smooth' });
    },

    get carbonSavingsText() {
        if (!this.selectedProduct) {
            return '';
        }

        const savings = Math.round(((8.5 - this.selectedProduct.emission) / 8.5) * 100);

        return `${savings > 0 ? '-' + savings : '+' + Math.abs(savings)}% vs Katun Konvensional`;
    },
});
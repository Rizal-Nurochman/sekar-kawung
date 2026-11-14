export const catalog = (products = []) => ({
    products,
    filter: 'all',

    setFilter(filter) {
        this.filter = filter;
    },

    openProduct(id) {
        const product = this.products.find((item) => item.id === id);

        if (!product) {
            return;
        }

        this.$dispatch('open-product', { product });
    },
});
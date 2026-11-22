const trimNumber = (value, precision) => {
    const fixed = Number(value).toFixed(precision);

    return fixed.includes('.') ? fixed.replace(/\.?0+$/, '') : fixed;
};

const formatEmission = (value) => trimNumber(Number(value) || 0, 4);

export const adminCalculator = () => ({
    collapsed: [],
    total: 0,
    filled: 0,
    rowCount: 0,

    init() {
        this.rowCount = this.rows().length;
        this.recalculate();
    },

    rows() {
        return Array.from(this.$el.querySelectorAll('[data-calculator-row]'));
    },

    /**
     * A row's emission is quantity × factor, where the factor comes from the
     * selected option when the activity has options, and from the activity
     * itself otherwise.
     */
    rowTotal(row) {
        const quantityInput = row.querySelector('[data-quantity-input]');
        const quantity = Number.parseFloat(quantityInput?.value ?? '0') || 0;

        if (quantity === 0) {
            return 0;
        }

        let factor = Number.parseFloat(quantityInput?.dataset.baseEf ?? '0') || 0;
        const select = row.querySelector('[data-option-select]');

        if (select) {
            if (!select.value) {
                return 0;
            }

            const option = select.options[select.selectedIndex];
            factor = Number.parseFloat(option?.dataset.ef ?? '0') || 0;
        }

        return quantity * factor;
    },

    recalculate() {
        let total = 0;
        let filled = 0;

        for (const row of this.rows()) {
            total += this.rowTotal(row);

            const quantity = Number.parseFloat(row.querySelector('[data-quantity-input]')?.value ?? '0') || 0;

            if (quantity > 0) {
                filled++;
            }
        }

        this.total = total;
        this.filled = filled;
    },

    get formattedTotal() {
        return formatEmission(this.total);
    },

    get progress() {
        if (this.rowCount === 0) {
            return 0;
        }

        return Math.min(100, Math.round((this.filled / this.rowCount) * 100));
    },

    toggleStage(key) {
        this.collapsed = this.collapsed.includes(key)
            ? this.collapsed.filter((item) => item !== key)
            : [...this.collapsed, key];
    },

    isCollapsed(key) {
        return this.collapsed.includes(key);
    },

    collapseAll() {
        this.collapsed = Array.from(this.$el.querySelectorAll('[data-calculator-stage]')).map((stage) => stage.dataset.stageKey);
    },

    expandAll() {
        this.collapsed = [];
    },

    get allCollapsed() {
        const stages = Array.from(this.$el.querySelectorAll('[data-calculator-stage]'));

        return stages.length > 0 && stages.every((stage) => this.collapsed.includes(stage.dataset.stageKey));
    },

    /**
     * Number inputs reject HTMLInputElement.select() in Chrome, so the call
     * is guarded: focusing a prefilled quantity should replace it, not append.
     */
    selectOnFocus(event) {
        try {
            event.target.select();
        } catch (error) {
            /* number inputs do not support text selection in every browser */
        }
    },
});

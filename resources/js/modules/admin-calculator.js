const trimNumber = (value, precision) => {
    const fixed = Number(value).toFixed(precision);

    return fixed.includes('.') ? fixed.replace(/\.?0+$/, '') : fixed;
};

const formatEmission = (value) => trimNumber(Number(value) || 0, 4);

export const adminCalculator = (params = {}) => ({
    baseSchemeTotals: Array.isArray(params.baseSchemeTotals) ? params.baseSchemeTotals : [],
    currentSchemeId: Number(params.currentSchemeId ?? 0) || 0,
    stepTotal: 0,
    rowCount: 0,

    init() {
        this.rowCount = this.rows().length;
        this.recalculate();
        this.$el.addEventListener('input', () => this.recalculate());
        this.$el.addEventListener('change', () => this.recalculate());
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
        let stepTotal = 0;

        for (const row of this.rows()) {
            stepTotal += this.rowTotal(row);
        }

        this.stepTotal = stepTotal;
    },

    /**
     * Per-scheme running totals. The scheme currently being filled receives
     * the live step total; every other scheme keeps its saved subtotal.
     */
    get schemeTotals() {
        return this.baseSchemeTotals.map((scheme) => {
            const total = Number(scheme.total) + (Number(scheme.id) === this.currentSchemeId ? this.stepTotal : 0);

            return {
                id: scheme.id,
                name: scheme.name,
                total,
                formattedTotal: formatEmission(total),
            };
        });
    },

    /**
     * Keep the read-only unit pill in sync with the chosen option. Falls back
     * to the activity unit (or blank) when the option has no unit.
     */
    syncUnit(event, fallback = '') {
        const row = event.target?.closest('[data-calculator-row]');
        const pill = row?.querySelector('[data-unit-pill]');

        if (pill) {
            const selected = event.target.options?.[event.target.selectedIndex];
            const unit = selected?.dataset.unit || fallback || '';

            pill.textContent = unit;
            pill.title = unit;
        }

        this.recalculate();
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

const trimNumber = (value, precision) => {
    const fixed = Number(value).toFixed(precision);

    return fixed.includes('.') ? fixed.replace(/\.?0+$/, '') : fixed;
};

const formatEmission = (value) => trimNumber(Number(value) || 0, 4);

export const adminCalculator = (params = {}) => ({
    baseTotal: Number(params.baseTotal ?? 0) || 0,
    baseFilled: Number(params.baseFilled ?? 0) || 0,
    totalActivities: Number(params.totalActivities ?? 0) || 0,
    stepTotal: 0,
    stepFilled: 0,
    total: Number(params.baseTotal ?? 0) || 0,
    filled: Number(params.baseFilled ?? 0) || 0,
    rowCount: 0,

    init() {
        this.rowCount = this.rows().length;

        for (const pill of this.$el.querySelectorAll('[data-unit-pill]')) {
            this.fitUnitPill(pill);
        }

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
        let stepFilled = 0;

        for (const row of this.rows()) {
            stepTotal += this.rowTotal(row);

            const quantity = Number.parseFloat(row.querySelector('[data-quantity-input]')?.value ?? '0') || 0;

            if (quantity > 0) {
                stepFilled++;
            }
        }

        this.stepTotal = stepTotal;
        this.stepFilled = stepFilled;
        this.total = this.baseTotal + stepTotal;
        this.filled = this.baseFilled + stepFilled;
    },

    get formattedTotal() {
        return formatEmission(this.total);
    },

    get progress() {
        if (this.totalActivities === 0) {
            return 0;
        }

        return Math.min(100, Math.round((this.filled / this.totalActivities) * 100));
    },

    /**
     * Keep the read-only unit pill in sync with the chosen option. Falls back
     * to the activity unit (or blank) when the option has no unit. The pill
     * keeps its fixed width, so long labels shrink to 12px instead.
     */
    syncUnit(event, fallback = '') {
        const row = event.target?.closest('[data-calculator-row]');
        const pill = row?.querySelector('[data-unit-pill]');

        if (pill) {
            const selected = event.target.options?.[event.target.selectedIndex];
            const unit = selected?.dataset.unit || fallback || '';

            pill.textContent = unit;
            this.fitUnitPill(pill, unit);
        }

        this.recalculate();
    },

    fitUnitPill(pill, unit = null) {
        const text = unit ?? pill.textContent ?? '';
        const compact = text.trim().length > 12;

        pill.classList.toggle('text-xs', compact);
        pill.classList.toggle('text-[14px]', !compact);
        pill.dataset.unitCompact = compact ? '1' : '0';
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

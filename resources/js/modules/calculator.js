export const calculator = (defaults = {}) => ({
    material: defaults.material ?? 3.8,
    energy: defaults.energy ?? 1.0,
    quantity: defaults.quantity ?? 1000,
    distance: defaults.distance ?? 250,
    defaults,
    chart: null,

    init() {
        this.$nextTick(() => {
            this.createChart();

            this.$watch(
                () => [this.material, this.energy, this.quantity, this.distance],
                () => this.updateChart(),
            );
        });
    },

    get quantityValue() {
        return Number(this.quantity) || 0;
    },

    get materialEmission() {
        return this.quantityValue * Number(this.material);
    },

    get energyEmission() {
        return this.quantityValue * 1.2 * Number(this.energy);
    },

    get logisticsEmission() {
        return this.quantityValue * Number(this.distance) * 0.0001;
    },

    get totalEmission() {
        return this.materialEmission + this.energyEmission + this.logisticsEmission;
    },

    get standardIndustryTotal() {
        return this.quantityValue * 10;
    },

    get diffPercentage() {
        if (this.standardIndustryTotal <= 0) {
            return 0;
        }

        return Math.round(((this.standardIndustryTotal - this.totalEmission) / this.standardIndustryTotal) * 100);
    },

    get isLowerThanIndustry() {
        return this.diffPercentage >= 0;
    },

    get comparisonText() {
        return this.isLowerThanIndustry
            ? `${this.diffPercentage}% Lebih Rendah dari Rata-rata Industri`
            : `${Math.abs(this.diffPercentage)}% Lebih Tinggi dari Rata-rata Industri`;
    },

    get badgeIcon() {
        return this.isLowerThanIndustry ? 'fa-leaf' : 'fa-triangle-exclamation';
    },

    get recommendations() {
        const recommendations = [];

        if (Number(this.energy) > 0.5) {
            recommendations.push('Pertimbangkan beralih ke sumber energi surya/terbarukan untuk memangkas emisi energi hingga 75%.');
        } else {
            recommendations.push('Penggunaan energi terbarukan di fasilitas Anda sudah optimal.');
        }

        if (Number(this.material) > 5.0) {
            recommendations.push('Pilih opsi material daur ulang (Recycled/Organic) untuk menekan emisi mentah bahan baku.');
        } else {
            recommendations.push('Pemilihan material tekstil Anda memiliki standar karbon yang sangat baik.');
        }

        recommendations.push('Gunakan mitra logistik berbasis kendaraan listrik/terkonsolidasi untuk efisi rute.');

        return recommendations;
    },

    formatNumber(value) {
        return Math.round(value).toLocaleString('id-ID');
    },

    createChart() {
        const canvas = this.$refs.chart;

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        this.chart = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Material', 'Energi', 'Logistik'],
                datasets: [{
                    data: [1, 1, 1],
                    backgroundColor: ['#10b981', '#3b82b6', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: !window.matchMedia('(prefers-reduced-motion: reduce)').matches,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => ` ${context.label}: ${Math.round(context.raw)} kg CO₂e`,
                        },
                    },
                },
                cutout: '75%',
            },
        });

        this.updateChart();
    },

    updateChart() {
        if (!this.chart) {
            return;
        }

        this.chart.data.datasets[0].data = [
            this.materialEmission,
            this.energyEmission,
            this.logisticsEmission,
        ];
        this.chart.update();
    },

    loadProduct({ emission }) {
        const match = Array.from(this.$refs.materialSelect.options)
            .find((option) => Math.abs(Number(option.value) - Number(emission)) < 0.001);

        if (match) {
            this.material = Number(match.value);
        }

        this.energy = Number(this.defaults.energy ?? 1.0);
        this.quantity = Number(this.defaults.quantity ?? 1000);
        this.distance = Number(this.defaults.distance ?? 250);
    },
});
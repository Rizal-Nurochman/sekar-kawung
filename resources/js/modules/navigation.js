export const navigation = () => ({
    navOpen: false,

    toggleNav() {
        this.navOpen = !this.navOpen;
    },

    closeNav() {
        this.navOpen = false;
    },
});
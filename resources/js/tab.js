Ignition.registerTab((Vue) => {
    // ToDo: https://github.com/roszpun/vue-collapse
    Vue.use(require('vue2-collapse'));
    Vue.component('ignition-github', require('./components/Tab'));
});

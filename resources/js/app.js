/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


//vform
import { Form, HasError, AlertError } from 'vform';
//moment js
import moment from 'moment';
//vue router
import VueRouter from 'vue-router';
//vue progressbar
import VueProgressBar from 'vue-progressbar';
//sweetalert
import swal from 'sweetalert2';
//redirectuser
import Gate from './Gate';
//redirectUser
Vue.prototype.$gate = new Gate(window.user);


window.swal = swal;

window.Vue = require('vue');
window.Form = Form;

//reload
window.reload = new Vue();



//vue vform
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

//vue router
Vue.use(VueRouter);

//vue filter
Vue.filter('capitalize', function(value) {
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
});
Vue.filter('userDateCreated', function(datecreated) {

    return moment(datecreated).format("MMM Do YY");
});

//progressbar
Vue.use(VueProgressBar, {
    color: 'rgb(178, 255, 102)',
    failedColor: 'red',
    height: '4px'
});

//vue router
let routes = [{
    path: '/dashboard',
    component: require('./components/Dashboard.vue').default
}, {
    path: '/profile',
    component: require('./components/Profile.vue').default
}, {
    path: '/users',
    component: require('./components/Users.vue').default
}, {
    path: '/developer',
    component: require('./components/Developer.vue').default
}];




const router = new VueRouter({
    mode: 'history',
    routes, // short for `routes: routes`
    linkActiveClass: 'active'

});

const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', swal.stopTimer)
        toast.addEventListener('mouseleave', swal.resumeTimer)
    }
});
//sweetalert
window.toast = toast;


Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);





/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router
});

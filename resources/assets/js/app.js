
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
 require('./bootstrap');
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import VueResource from 'vue-resource'
import MobileDetect from 'mobile-detect'

window.Vue = require('vue');

window.Vue.use(VueResource)

window.Vue.http.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;


window.Vue.prototype.$qrcode = function(href){
  if (!href) {
    href = window.location.href;
  }
  return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(href);
}

window.Vue.prototype.$md = new MobileDetect(window.navigator.userAgent);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


const app = new Vue({
    el: '#app',
    router,
    i18n,
    template: '<App/>',
  	components: { App }
});
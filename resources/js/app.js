/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// window.Vue = require('vue');
import Vue from 'vue';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// IMPORT COMPONENT TERSEBUT

import Mesages from './components/Messages.vue'
import Form from './components/Form.vue'

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// REGISTER COMPONENT TERSEBUT SECARA GLOBAL
//PARAMATER PERTAMA ADALAH CUSTOM TAG YANG DIINGINKAN, PARAMETER KEDUA ADALAH NAMA COMPONENTNYA

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('dw-messages', Messages);
Vue.component('dw-form', Form);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',

    data: {
        // VARIABLE UNTUK MENANMPUNG DATA PESAN
        messages: []
    },

    // KETIKA FILE INI DI-LOAD ATAU AKAN DI-RENDER OLEH BROWSER
    created() {
        // MAKA AKAN MENJALANKAN FUNGSI fetchMessages()
        this.fetchMessages();

        // DAN MENGGUNAKAN LARAVEL ECHO, KITA AKSES PRVATE CHANNEL BERNAMA CHAT YANG NANTINYA AKAN DIBUAT
        // KEMUDIAN EVENTNYA DI LISTEN ATAU PANTAU JIKA ADA DATA YANG DIKIRIM
        Echo.private('chat')
        .listen('MessageSent', (e) => {
            // DATA YANG DITERIMA AKAN DITAMBAHKAN KE DALAM VARIABLE MESSAGE SEBELUMNYA
            this.messages.push({
                message: e.message.message,
                user: e.user
            })
        })
    },
    methods: {
        // FUNGSI FETCH MESSAGE UNTUK MEMINTA DATA DARI DATABASE TERKAIT PESAN YANG SUDAH LAMPAU
        fetchMessages() {
            // MENGGUNAKAN AXIOS UNTUK MELAKUKAN AJAX REQUEST
            axios.get('/messages').then(response => {
                // SETIAP DATA YANG DITERIMA AKAN DITAMBAHKAN KE VARIABLE MESSAGES
                this.messages = response.data
            });
        },

        // EMIT YANG DIKIRIMKAN AKAN DI-HANDLE DISINI
        // UNTUK TRACE-NYA, BISA DILIHAT DI FILE chat.blade.php. DISANA TERDAPAT ATTRIBUTE v-on:sent="addMessage" DI DALAM TAG DW-FORM
        // YANG ARTINYA KETIKA EMIT BERNAMA SENT DITERIMA, MAKA AKAN MEMICU FUNGSI addMessage
        addMessage(message) {
            // PESAN YANG DITERIMA AKAN DITAMBAHKAN KE VARIABLE MESSAGE
            this.messages.push(message);

            //KEMUDIAN AKAN DISIMPAN KE DATABASE SEBAGAI LOG
            axios.post('/messages', message).then(response => {
                console.log(response.data);
            });
        }
    }
});

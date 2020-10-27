
window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    //require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});



function displayNotification(type, data) {
    let message = "";
    $(".btn-notifications").addClass("new");
    if(type == "convite") 
    {
        message = "Convidou você para o evento:";
        $(".btn-tab").eq(2).addClass("new");
    }

    else if(type == "cancelado") 
    {
        message = "Cancelou o evento:";
    }   

    else if(type == "aceito")
    {
        message = "Aceitou seu convite para o evento:";
    }

    let time = Date.now();

    let template = `
        <div class="item" id="${time}">
            <p>
            <strong>${data.user.name}</strong>
                ${message}      
            <strong>${data.title || data.event.title}</strong>
            </p>
        </div>
    `;
    $(".notifications").append(template);

    setTimeout(() => {
        $(".notifications").find(`.item#${time}`).fadeOut();
    }, 3500);

}

window.Echo.private(`App.User.${Laravel.user}`)
    .notification(notification => {
        console.log(notification);
        notification.type = notification.type.replace(/\\/g, "/");
        if(notification.type == "convite" || notification.type == 'App/Notifications/SendInvitation') {
            return displayNotification("convite", notification.event);
        }

        if(notification.type == "cancelado" || notification.type == "App/Notifications/CanceledEvent") {
            return displayNotification("cancelado", notification.event);
        } 
        
        if(notification.type == "aceito" || notification.type == "App/Notifications/InvitationAccepted") {
            return displayNotification("aceito", notification);
        } 
    });
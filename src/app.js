import Vue from 'vue';
import Router from 'vue-router';

import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';

import Dashboard from './Components/Dashboard';
import ErrorLogs from './Components/Logs';
import NotifcationSettings from './Components/NotificationSettings';

import {
    Menu,
    MenuItem,
    Table,
    TableColumn,
    Pagination,
    Input,
    Select,
    Option,
    Button,
    RadioGroup,
    RadioButton,
    Radio,
    Tabs,
    TabPane,
    ButtonGroup,
    Tooltip,
    InputNumber,
    Message,
    Notification,
    Loading,
    MessageBox
} from 'element-ui';

Vue.use(Menu);
Vue.use(Tabs);
Vue.use(TabPane);
Vue.use(MenuItem);
Vue.use(Tooltip);
Vue.use(Table);
Vue.use(TableColumn);
Vue.use(Pagination);
Vue.use(Input);
Vue.use(InputNumber);
Vue.use(Select);
Vue.use(Button);
Vue.use(ButtonGroup);
Vue.use(Option);
Vue.use(Radio);
Vue.use(RadioGroup);
Vue.use(RadioButton);

Vue.prototype.$notify = Notification;
Vue.prototype.$message = Message;
Vue.prototype.$msgbox = MessageBox;

Vue.prototype.$confirm = MessageBox.confirm;

Vue.use(Loading.directive);
Vue.prototype.$loading = Loading.service;

locale.use(lang);

Vue.use(Router);

const vueRouter = new Router({
    routes: [
        {
            name: 'home',
            path: '/',
            component: Dashboard
        },
        {
            name: 'logs',
            path: '/logs',
            component: ErrorLogs
        },
        {
            name: 'notifications',
            path: '/notification-settings',
            component: NotifcationSettings
        }
    ]
});

Vue.mixin({
    data() {
        return {
            adminVars: window.ninjaErrorAdminVars
        }
    },
    methods: {
        $get(url, data) {
            if (!data) {
                data = {};
            }
            data.action = this.adminVars.ajax_action;
            data.route = url;
            return jQuery.get(this.adminVars.ajax_url, data);
        },
        $post(url, data) {
            if (!data) {
                data = {};
            }
            data.action = this.adminVars.ajax_action;
            data.route = url;
            return jQuery.post(this.adminVars.ajax_url, data);
        }
    }
});

new Vue({
    el: '#ninja_error_log_app',
    render: h => h(require('./Application').default),
    router: vueRouter,
    mounted() {
    }
});

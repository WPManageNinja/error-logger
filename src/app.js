import Vue from 'vue';
import Router from 'vue-router';

import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';

import Dashboard from './Components/Dashboard';
import ErrorLogs from './Components/Logs';
import NotifcationSettings from './Components/NotificationSettings';

import 'element-ui/lib/theme-chalk/index.css';

import {
    Pagination,
    Dialog,
    Autocomplete,
    Dropdown,
    DropdownMenu,
    DropdownItem,
    Menu,
    Submenu,
    MenuItem,
    MenuItemGroup,
    Input,
    InputNumber,
    Radio,
    RadioGroup,
    RadioButton,
    Checkbox,
    CheckboxButton,
    CheckboxGroup,
    Switch,
    Select,
    Option,
    OptionGroup,
    Button,
    ButtonGroup,
    Table,
    TableColumn,
    DatePicker,
    TimeSelect,
    TimePicker,
    Popover,
    Tooltip,
    Breadcrumb,
    BreadcrumbItem,
    Form,
    FormItem,
    Tabs,
    TabPane,
    Tag,
    Tree,
    Alert,
    Slider,
    Icon,
    Row,
    Col,
    Upload,
    Progress,
    Spinner,
    Badge,
    Card,
    Rate,
    Steps,
    Step,
    Carousel,
    CarouselItem,
    Collapse,
    CollapseItem,
    Cascader,
    ColorPicker,
    Transfer,
    Container,
    Header,
    Aside,
    Main,
    Footer,
    Timeline,
    TimelineItem,
    Link,
    Divider,
    Image,
    Calendar,
    Backtop,
    PageHeader,
    CascaderPanel,
    Loading,
    MessageBox,
    Message,
    Notification
} from 'element-ui';

Vue.use(Pagination);
Vue.use(Dialog);
Vue.use(Autocomplete);
Vue.use(Dropdown);
Vue.use(DropdownMenu);
Vue.use(DropdownItem);
Vue.use(Menu);
Vue.use(Submenu);
Vue.use(MenuItem);
Vue.use(MenuItemGroup);
Vue.use(Input);
Vue.use(InputNumber);
Vue.use(Radio);
Vue.use(RadioGroup);
Vue.use(RadioButton);
Vue.use(Checkbox);
Vue.use(CheckboxButton);
Vue.use(CheckboxGroup);
Vue.use(Switch);
Vue.use(Select);
Vue.use(Option);
Vue.use(OptionGroup);
Vue.use(Button);
Vue.use(ButtonGroup);
Vue.use(Table);
Vue.use(TableColumn);
Vue.use(DatePicker);
Vue.use(TimeSelect);
Vue.use(TimePicker);
Vue.use(Popover);
Vue.use(Tooltip);
Vue.use(Breadcrumb);
Vue.use(BreadcrumbItem);
Vue.use(Form);
Vue.use(FormItem);
Vue.use(Tabs);
Vue.use(TabPane);
Vue.use(Tag);
Vue.use(Tree);
Vue.use(Alert);
Vue.use(Slider);
Vue.use(Icon);
Vue.use(Row);
Vue.use(Col);
Vue.use(Upload);
Vue.use(Progress);
Vue.use(Spinner);
Vue.use(Badge);
Vue.use(Card);
Vue.use(Rate);
Vue.use(Steps);
Vue.use(Step);
Vue.use(Carousel);
Vue.use(CarouselItem);
Vue.use(Collapse);
Vue.use(CollapseItem);
Vue.use(Cascader);
Vue.use(ColorPicker);
Vue.use(Transfer);
Vue.use(Container);
Vue.use(Header);
Vue.use(Aside);
Vue.use(Main);
Vue.use(Footer);
Vue.use(Timeline);
Vue.use(TimelineItem);
Vue.use(Link);
Vue.use(Divider);
Vue.use(Image);
Vue.use(Calendar);
Vue.use(Backtop);
Vue.use(PageHeader);
Vue.use(CascaderPanel);

Vue.use(Loading.directive);

Vue.prototype.$loading = Loading.service;
Vue.prototype.$msgbox = MessageBox;
Vue.prototype.$alert = MessageBox.alert;
Vue.prototype.$confirm = MessageBox.confirm;
Vue.prototype.$prompt = MessageBox.prompt;
Vue.prototype.$notify = Notification;
Vue.prototype.$message = Message;

// import {
//     Menu,
//     MenuItem,
//     Table,
//     TableColumn,
//     Pagination,
//     Input,
//     Select,
//     Option,
//     Button,
//     RadioGroup,
//     RadioButton,
//     Radio,
//     Tabs,
//     TabPane,
//     ButtonGroup,
//     Tooltip,
//     InputNumber,
//     Message,
//     Notification,
//     Loading,
//     MessageBox
// } from 'element-ui';

// Vue.use(Menu);
// Vue.use(Tabs);
// Vue.use(TabPane);
// Vue.use(MenuItem);
// Vue.use(Tooltip);
// Vue.use(Table);
// Vue.use(TableColumn);
// Vue.use(Pagination);
// Vue.use(Input);
// Vue.use(InputNumber);
// Vue.use(Select);
// Vue.use(Button);
// Vue.use(ButtonGroup);
// Vue.use(Option);
// Vue.use(Radio);
// Vue.use(RadioGroup);
// Vue.use(RadioButton);

// Vue.prototype.$notify = Notification;
// Vue.prototype.$message = Message;
// Vue.prototype.$msgbox = MessageBox;

// Vue.prototype.$confirm = MessageBox.confirm;

// Vue.use(Loading.directive);
// Vue.prototype.$loading = Loading.service;

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

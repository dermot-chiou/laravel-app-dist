import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);

const messages = {
    'en-US': {
        pharaoh: {
            app_download: 'APP DOWNLOAD',
            warrior: 'Warrior',
            golden_cat: 'Golden Cat',
            pharaoh_poker: 'Pharaoh Poker',
            golden_option: 'Golden Option',
            sytepoker: 'SytePoker',
            livecasino: 'Live Casino',
            download: 'Download'
        }
    },
    'en' : {
        pharaoh: {
            app_download: 'APP DOWNLOAD',
            warrior: 'Warrior',
            golden_cat: 'Golden Cat',
            pharaoh_poker: 'Pharaoh Poker',
            golden_option: 'Golden Option',
            sytepoker: 'SytePoker',
            livecasino: 'Live Casino',
            download: 'Download',
            supportedDevice: 'Supported device',
            unspported: 'Does not support',
            unspportedDescription: 'Your device does not support. Please download the app by scaning QR Code',
            Android: 'Android',
            iOS: 'iOS',
            close: 'Close',
        }
    },
    'zh-TW' : {
        pharaoh: {
            app_download: '手機下載',
            warrior: '武士道',
            golden_cat: '金錢貓',
            pharaoh_poker: '法老撲克',
            golden_option: '黃金期權',
            sytepoker: '賽特撲克',
            livecasino: '真人荷官',
            download: '下載',
            supportedDevice: '支援裝置',
            unspported: '不支援',
            unspportedDescription: '你的裝置不支援，請用手機掃描QR Code下載',
            Android: 'Android',
            iOS: 'iOS',
            close: '關閉',
        }
    },
    'zh-CN' :{
        pharaoh: {
            app_download: '手機下載',
            warrior: '武士道',
            golden_cat: '金钱猫',
            pharaoh_poker: '法老扑克',
            golden_option: '黄金期权',
            sytepoker: '赛特扑克',
            livecasino: '真人荷官',
            download: '下载',
            supportedDevice: '支持装置',
            unspported: '不支持',
            unspportedDescription: '你的装置不支持，请用手机扫描二维码下载',
            Android: '安卓',
            iOS: '苹果',
            close: '关闭',
        }
    },
    'zh' :{
        pharaoh: {
            app_download: '手机下载',
            warrior: '武士道',
            golden_cat: '金钱猫',
            pharaoh_poker: '法老扑克',
            golden_option: '黄金期权',
            sytepoker: '赛特扑克',
            livecasino: '真人荷官',
            download: '下载',
            supportedDevice: '支持装置',
            unspported: '不支持',
            unspportedDescription: '你的装置不支持，请用手机扫描二维码下载',
            Android: '安卓',
            iOS: '苹果',
            close: '关闭',
        }
    }
}


export default new VueI18n({
    locale: messages[window.navigator.languages[0]] != null ? window.navigator.languages[0] : 'en-US',
    messages
})
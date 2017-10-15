import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);

const messages = {
    'en-US': {
        pharaoh: {
            app_download: 'Pharaoh Apps Download',
            warrior: 'Warrior',
            golden_cat: 'Golden Cat',
            pharaoh_poker: 'Pharaoh Poker',
            golden_option: 'Golden Option',
            sytepoker: 'SytePoker',
            livecasino: 'Live Casino',
            download: 'Download'
        }
    },
    'zh-TW' : {
        pharaoh: {
            app_download: '法老 APP 下載',
            warrior: '武士道',
            golden_cat: '金錢貓',
            pharaoh_poker: '法老撲克',
            golden_option: '黃金期權',
            sytepoker: '賽特撲克',
            livecasino: '真人荷官',
            download: '下載'
        }
    }
}


export default new VueI18n({
    locale: messages[window.navigator.language] != null ? window.navigator.language : 'en-US',
    messages
})
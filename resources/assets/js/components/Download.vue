<template>
    <div>
        <h4>{{app.name}}</h4>
        <p>支援裝置</p>
        <p>
            <span v-for="os in osList" class="label label-success">{{os}}</span> &nbsp;
        </p>
        <img class="qrcode img-thumbnail" :src="this.$qrcode()" alt="">
        <vue-markdown :source="app.description"></vue-markdown>
    </div>
</template>
<style>
    .label{
        margin: 0 2px;
    }

    th{
        text-align: center;
    }
</style>
<script>
    import VueMarkdown from 'vue-markdown'
    export default {
        data() {
            return {
              app: {
                name :''
              },
              osList: [],
        }},
        components: {
            VueMarkdown
        },
        mounted() {
            this.$http.get('/apps/' + this.$route.params.appId).then(response => {
                this.app = response.body;
                window.document.title = "法老王 APP " + this.app.name
                var url = {};
                var that = this;
                this.app.files.map(function(v, k)
                {
                    if(!url[v.os])
                        url[v.os] = {};
                    url[v.os][v.device] = v.url;

                    if(!that.osList.includes(v.os))
                        that.osList.push(v.os);
                });


                var link = null;
                if (this.$md.is('AndroidOS') && url.Android) {
                    if (this.$md.phone()) {
                        link = url.Android.phone || url.Android.tablet;
                    }

                    if (this.$md.tablet()) {
                        link = url.Android.phone || url.Android.mobile;
                    }
                }
                else if (this.$md.is('iOS') && url.iOS) {
                    if (this.$md.phone()) {
                        link = url.iOS.phone || url.iOS.tablet;
                    }

                    if (this.$md.tablet()) {
                        link = url.iOS.phone || url.iOS.mobile;
                    }
                }
                if(link)
                    window.location.href = link;
                else
                    alert('你的裝置不支援，請用手機掃描QR Code下載');
                
            }, response => {

            });
        }
    }
</script>

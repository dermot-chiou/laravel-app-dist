<template>
    <div>
        <h4>{{apps.info.name}}</h4>
        <img class="qrcode img-thumbnail" :src="this.$qrcode()" alt="">
    </div>
</template>

<script>
    export default {
        data() {
            return {
              apps: {
                info: {
                    name :''
                }
              }
            }},
        mounted() {
            this.$http.get('/apps/' + this.$route.params.appId).then(response => {
                this.apps = response.body;
                if (this.$md.is('AndroidOS')) {
                    if (this.$md.phone()) {
                        var url = (this.apps.urls.Android.phone || this.apps.urls.Android.tablet);
                        window.location.href = (url.url);    
                        return;
                    }

                    if (this.$md.tablet()) {
                        var url = (this.apps.urls.Android.tablet || this.apps.urls.Android.phone);
                        window.location.href = (url.url);    
                        return;
                    }
                    
                }
                else if (this.$md.is('iOS')) {
                    if (this.$md.phone()) {
                        var url = (this.apps.urls.iOS.phone || this.apps.urls.iOS.tablet);
                        window.location.href = (url.url);    
                        return;
                    }

                    if (this.$md.tablet()) {
                        var url = (this.apps.urls.iOS.tablet || this.apps.urls.iOS.phone);
                        window.location.href = (url.url);    
                        return;
                    }
                }

                alert('你的裝置不支援，請用手機掃描QR Code下載');
                
            }, response => {

            });
        }
    }
</script>

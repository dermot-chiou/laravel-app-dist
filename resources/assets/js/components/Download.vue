<template>
    <div>
        <h4>{{app.name}}</h4>
        <p>{{$t('pharaoh.supportedDevice')}}</p>
        <p>
            <span v-for="os in osList" class="label label-success">{{$t('pharaoh.' + os)}}</span> &nbsp;
        </p>
        <qriously :value="href" :size="200" class="qrcode img-thumbnail"></qriously>

        <vue-markdown :source="app.description" v-show="app.description"></vue-markdown>

        <div class="modal fade not-support" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">{{$t('pharaoh.unspported')}}</h5>
                </div>
                <div class="modal-body">
                    <h6>
                        {{$t('pharaoh.unspportedDescription')}}
                    </h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">{{$t('pharaoh.close')}}</button>
                </div>
            </div>
        </div>
        </div>
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
        computed: {
            href: function(){
                return window.location.href;
            }
        },
        components: {
            VueMarkdown
        },
        mounted() {
            this.$http.get('/apps/' + this.$route.params.appId).then(response => {
                this.app = response.body;
                window.document.title = "PHARAOH APP " + this.app.name
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
                        link = url.iOS.tablet || url.iOS.phone;
                    }
                }
                console.log(link);
                if(link)
                    window.location.href = link;
                else
                    window.$('.not-support').modal('show');
                
            }, response => {

            });
        }
    }
</script>

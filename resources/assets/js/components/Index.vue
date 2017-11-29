<template>
    <div>
        <div class="row">
            <a v-for="(app, index) in apps" :href="rootUrl + '/download/#/' + app.app_id" class="btn btn-lg btn-primary col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-4" :class="[(index % 2 == 0)? 'col-md-offset-1' : 'col-md-offset-2']" >{{app.name}}</a>
        </div>
    </div>
</template>
<style>
    .row{
        margin: 5px 0;
    }
    .btn{
        margin-bottom: 10px;
    }
</style>
<script>
    export default {
        data() {
            return {
              apps: ''
            }},
        computed: {
            rootUrl: function() {
                return window.location.origin;
            }
        },
        mounted() {
            this.$http.get('/apps').then(response => {
                this.apps = response.body;
            }, response => {

            });
        }
    }
</script>

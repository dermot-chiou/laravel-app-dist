<template>
    <div>
        <div class="row" v-for="app in apps" :key="app.app_id">
            <router-link :to="app.app_id" class="btn btn-lg btn-primary col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-2 col-md-offset-5" >{{app.name}}</router-link>
        </div>
    </div>
</template>
<style>
    .row{
        margin: 5px 0;
    }
</style>
<script>
    export default {
        data() {
            return {
              apps: ''
            }},
        mounted() {
            this.$http.get('/apps').then(response => {
                this.apps = response.body;
                console.log(this.apps);
            }, response => {

            });
        }
    }
</script>

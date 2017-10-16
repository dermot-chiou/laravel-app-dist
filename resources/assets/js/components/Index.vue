<template>
    <div>
        <router-link v-for="app in apps" :key="app.app_id" :to="app.app_id" class="btn btn-primary" >{{app.name}}</router-link>
    </div>
</template>

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

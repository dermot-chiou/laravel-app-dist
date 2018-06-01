<template>
  <div id="app" class="container">
    <!--<img src="/images/logo.png">-->
    <h1>{{ $t('pharaoh.app_download') }}</h1>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'app'
}
</script>

<style>
  body{
    text-align: center;
    background-color: #232323;
    color: #a4a4a4;
  }

  #app{
    margin-top: 60px;
  }

  ul{
		list-style: none;
		margin-top: 60px;
    padding: 0px;
	}

	ul li{
		padding: 0px;
		margin: 10px;
	}

  .qrcode{
    margin: 20px 0;
  }
</style>

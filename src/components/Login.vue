<template>
	<div class="p-absolute page">
        <GoogleLogin :params="params" :onSuccess="onSuccess" :onFailure="onFailure" class="btn-google btn-outline outline-white">Google Login</GoogleLogin>
	</div>
</template>

<style scoped>
	.page {
        background: url('/img/login.svg') no-repeat center center fixed; 
        background-size: cover;
    }
    .btn-google {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0,0,0,.5);
        color: white;
        width: 50vw;
        height: 40px;
    }      
</style>

<script>
import GoogleLogin from 'vue-google-login';
export default {
	data() {
		return {
            params: {
                client_id: "101148444110-h16o1ff180ed4at62auk00tojrq5t0jn.apps.googleusercontent.com"
            },
            renderParams: {
                width: 250,
                height: 50,
                longtitle: true
            },
		};
    },
    props: ['session', 'apiurl'],
    components: {
        GoogleLogin
    },
	methods: {
        onSuccess(googleUser) {
            var self = this
            var formData = new FormData()
            formData.append('USER_GMAIL', googleUser.getBasicProfile().getEmail())
            formData.append('USER_NAME', googleUser.getBasicProfile().getName())
            formData.append('USER_IMAGE', googleUser.getBasicProfile().getImageUrl())

            fetch(self.apiurl+'login.php', {
                method: 'post',
                body: formData
            }).then(function(response) {
                return response.json()
            }).then(function(response) {
                if(response.status=='ok') {
                    self.$emit('updateSession', response.user)
                    self.$router.push('/arena')
                }
                else {
                    console.log('Fail to login')
                }
            })
        },
        onFailure(error) {
            console.log('Google API fail: ', googleUser)
        }
	}
};
</script>

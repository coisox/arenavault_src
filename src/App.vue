<template>
	<div>
        <template v-if="isMobile" >
		    <router-view @childLogout="logout" @updateSession="updateSession" :session="session" :apiurl="apiurl"></router-view>
            <div class="footer" v-if="$route.path!='/login'">
                <div :class="{'red':$route.path=='/standing'}" @click="$router.push('/standing')"><i class="material-icons-outlined">local_convenience_store</i><div>Standing</div></div>
                <div :class="{'purple':$route.path=='/result'}" @click="$router.push('/result')"><i class="material-icons-outlined">offline_bolt</i><div>Result</div></div>
                <div :class="{'orange':$route.path=='/award'}" @click="$router.push('/award')"><i class="material-icons-outlined">emoji_events</i><div>Award</div></div>
                <div :class="{'green':$route.path=='/arena'}" @click="$router.push('/arena')"><i class="material-icons-outlined">account_balance</i><div>Arena</div></div>
            </div>
        </template>
        <div v-else>We're sorry but Arena Vault only works on smartphone (non laptop or tablet)</div>
	</div>
</template>

<style>
html, body { margin: 0; padding: 0; }

.navbar {
    background: url('/img/navbar.svg') no-repeat center top;
    height: 115px;
    width: 100vw;
    position: fixed;
}
.btn-circle {
    position: absolute;
    top: 15px;
    left: calc(50% - 35px);
    border-radius: 50%;
    width: 70px;
    height: 70px;
    border: 0;
    background-color: white;
    box-shadow: inset -0px 5px 15px rgba(0,0,0,.05), 0 5px 5px rgba(0, 0, 0, 0.3);
}
.btn-outline {
    border: 2px solid white;
    border-radius: 6px;
    background-color: transparent;
}
.footer {
    position: fixed;
    display: flex;
    align-items: center;
    bottom: 0;
    width: 100vw;
    height: 70px;
    background-color: white;
    box-shadow: 0 -5px 8px rgba(0, 0, 0, 0.15);
}
.footer > div {
    flex-grow: 1;
    text-align: center;
}

.page { width: 100vw; height: 100vh; }
.content-width { width: calc(100vw - 40px); }
.v-middle { vertical-align: middle; }
.p-relative { position: relative; }
.p-absolute { position: absolute; }
.p-fixed { position: fixed; }

.d-inline-block { display: inline-block; }
.d-flex { display: flex; }
.d-flex.center { justify-content: center; align-items: center; }
.d-flex.space-between { justify-content: space-between; align-items: center; }

.float-left { float: left; }
.float-right { float: right; }
.p-0 { padding: 0 !important; }
.px-10 { padding-left: 10px; padding-right: 10px; }
.py-10 { padding-top: 10px; padding-bottom: 10px; }
.pt-10 { padding-top: 10px !important; }
.pt-20 { padding-top: 20px !important; }
.pt-30 { padding-top: 30px !important; }
.pl-0 { padding-left: 0 !important; }
.pl-10 { padding-left: 10px !important; }
.pl-20 { padding-left: 20px !important; }
.pl-30 { padding-left: 30px !important; }
.pr-0 { padding-right: 0 !important; }
.pr-10 { padding-right: 10px !important; }
.pr-20 { padding-right: 20px !important; }
.pr-30 { padding-right: 30px !important; }
.m-0 { margin: 0 !important; }
.mt-10 { margin-top: 10px !important; }
.mt-20 { margin-top: 20px !important; }
.mt-30 { margin-top: 30px !important; }
.ml-0 { margin-left: 0 !important; }
.ml-10 { margin-left: 10px !important; }
.ml-20 { margin-left: 20px !important; }
.ml-30 { margin-left: 30px !important; }
.mr-0 { margin-right: 0 !important; }
.mr-10 { margin-right: 10px !important; }
.mr-20 { margin-right: 20px !important; }
.mr-30 { margin-right: 30px !important; }
.text-left { text-align: left; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.text-small { font-size: 12px; color: grey; }
.opacity-0 { opacity: 0; }

.white { color: white; }
.blue { color: #5A78D9; }
.orange { color: #FCB925; }
.purple { color: #956EE2; }
.green { color: #6CCE25; }
.red { color: #E9203E; }
.bg-gradient-blue { background: linear-gradient(180deg, #5A78D9 0%, #49A7EB 100%) !important; }
.bg-gradient-orange { background: linear-gradient(180deg, #FCB925 0%, #FFE136 100%) !important; }
.bg-gradient-purple { background: linear-gradient(180deg, #956EE2 0%, #C26EE2 100%) !important; }
.bg-gradient-green { background: linear-gradient(180deg, #6CCE25 0%, #B4EB45 100%) !important; }
.bg-gradient-red { background: linear-gradient(180deg, #E9203E 0%, #FF316E 100%) !important; }
.outline-blue { border-color: #5A78D9; }
.outline-orange { border-color: #FCB925; }
.outline-purple { border-color: #956EE2; }
.outline-green { border-color: #6CCE25; }
.outline-red { border-color: #E9203E; }

body, button i, input, select, .table-row {
    color: #616161;
    font: 16px Roboto;
}
*:focus {
    outline: none;
}
select, input, button {
    width: 100%;
    padding: 10px 0;
    box-sizing: border-box !important;
    border: 0;
}
select, input {
    border-bottom: 1px solid grey;
}
button {
    border-radius: 6px;
}
.nav-col {
    height: 70px;
    width: calc(50vw - 50px);
}
.nav-col:nth-child(2) {
    width: 100px;
}
.content {
    margin: 115px auto 0 auto;
    width: 100vw;
    height: calc(100vh - 195px);
    overflow-y: scroll;
}
.table-header, .table-row {
    width: calc(100vw - 40px);
    margin-left: 20px;
    padding: 10px;
    border-radius: 6px;
    border: 4px solid rgba(255,255,255,1);
    background-clip: padding-box;
    box-sizing: border-box;
    display: flex;
    align-items: center;
}
.table-header {
    background-color: #616161;
    color: white;
}
.table-row {
    border-color:rgba(255,255,255,.5);
    background-color: white;
}
.table-header i, .table-row i {
    vertical-align: middle;
}
.table-header + .table-row {
    margin-top: 57px;
}
.table-row + .table-row {
    margin-top: 10px;
}
.table-col-1 { flex: 0 0 10%; }
.table-col-2 { flex: 0 0 20%; }
.table-col-3 { flex: 0 0 30%; }
.table-col-4 { flex: 0 0 40%; }
.table-col-5 { flex: 0 0 50%; }
.table-col-6 { flex: 0 0 60%; }
.table-col-7 { flex: 0 0 70%; }
.table-col-8 { flex: 0 0 80%; }
.table-col-9 { flex: 0 0 90%; }
.table-col-10 { flex: 0 0 100%; }
.table-col-max { flex-grow: 1; }

.truncate {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

/* ============================================================================== tooltip */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: black;
    color: #fff;
    text-align: center;
    padding: 5px 0;
    border-radius: 6px;
    
    position: absolute;
    z-index: 1;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
}

/* ============================================================================== modal */
.modal-overlay {
    overflow-y: scroll;
    padding-bottom: 94px;
    box-sizing: border-box;
    width: 100vw;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background-color: rgba(0,0,0,.7);

    opacity: 0;
    pointer-events: none;
    transition: all .2s ease-in-out;
}
.modal-overlay.active {
    opacity: 1;
    pointer-events: initial;
}
.modal {
    width: calc(100vw - 48px);
    box-sizing: border-box;
    margin: 100px auto 0 auto;
    padding: 10px 20px;
    border-radius: 6px;
    box-shadow: 0px 0px 0px 4px rgba(255,255,255,.5);
    background-color: white;
    position: relative;
}
.modal .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 6px;
}
.modal-row {
    margin-top: 10px;
    text-align: center;
}
.modal-header {
    text-align: center;
    font-weight: bold;
    width: 80%;
    margin: auto;
}
.modal button {
    width: 100px;
}
.modal button + button {
    margin-left: 15px;
}
</style>

<script>
/*
npm install --save lingallery vue2-touch-events
*/
export default {
	name: "App",
	data() {
		return {
            session: {},
            apiurl: location.href.indexOf('localhost')>-1?'http://127.0.0.1//my/arenavault/api/':'',
			isMobile: false,
		}
	},
	mounted() {
        console.log('v20200217')

        window['app'] = this
        window.addEventListener("resize", this.checkIsMobile, { passive: true })
		this.checkIsMobile()
        this.session = JSON.parse(localStorage.getItem("ARENA_VAULT_SESSION") || '{}')
        this.checkSession()
	},
	methods: {
		checkIsMobile() {
			this.isMobile = window.innerWidth <= 600
		},
		checkSession() {
            console.log('In checkSession(), latest route: '+this.$route.path)
            
            if(!this.session.USER_ID && this.$route.path!='/login')  this.$router.push("/login")
			else if(this.session.USER_ID && !this.session.ARENA_ID && this.$route.path!='/arena') this.$router.push("/arena")
		},
		logout() {
			localStorage.removeItem("ARENA_VAULT_SESSION")
			this.checkSession()
        },
        updateSession(session) {
            this.session = session
        }
	},
	watch: {
		$route(to, from) {
			this.checkSession()
        },
        session(to, from) {
            localStorage.setItem('ARENA_VAULT_SESSION', JSON.stringify(to))
        }
	}
}
</script>
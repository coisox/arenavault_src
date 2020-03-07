<template>
	<div>
        <template v-if="isMobile" >
		    <router-view @childLogout="logout" @updateSession="updateSession" @updateTips="updateTips" @showToast="showToast" :session="session" :tips="tips" :apiurl="apiurl" :version="version"></router-view>
            <div class="footer" v-if="$route.path!='/login'">
                <div :class="{'red':$route.path=='/standing', 'dimm':!session.ARENA_ID}" @click="goto('/standing', !session.ARENA_ID)"><i class="material-icons-outlined">local_convenience_store</i><div>Standing</div></div>
                <div :class="{'purple':$route.path=='/result', 'dimm':!session.ARENA_ID || session.ARENA_ID=='000000'}" @click="goto('/result', !session.ARENA_ID || session.ARENA_ID=='000000')"><i class="material-icons-outlined">offline_bolt</i><div>Result</div></div>
                <div :class="{'orange':$route.path=='/emblem', 'dimm':!session.ARENA_ID || session.ARENA_ID=='000000'}" @click="goto('/emblem', !session.ARENA_ID || session.ARENA_ID=='000000')"><i class="material-icons-outlined">whatshot</i><div>Emblem</div></div>
                <div :class="{'green':$route.path=='/arena'}" @click="goto('/arena')"><i class="material-icons-outlined">account_balance</i><div>Arena</div></div>
            </div>
            <div class="toast" :class="{'active':toast.show}">{{toast.text}}</div>
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

.divider {
    width: 100%;
    height: 0;
    border-top: 1px solid #E0E0E0;
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
.pb-5 { padding-bottom: 5px !important; }
.pb-10 { padding-bottom: 10px !important; }
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
.w-100 { width: 100%; }

.white { color: white; }
.blue { color: #5A78D9; }
.orange { color: #FCB925; }
.purple { color: #956EE2; }
.green { color: #6CCE25; }
.red { color: #E9203E; }
.gold { color: gold; }
.silver { color: silver; }
.bronze { color: #CD7F32; }
.bg-gradient-blue { background: linear-gradient(180deg, #5A78D9 0%, #49A7EB 100%) !important; }
.bg-gradient-orange { background: linear-gradient(180deg, #FCB925 0%, #FFE136 100%) !important; }
.bg-gradient-purple { background: linear-gradient(180deg, #956EE2 0%, #C26EE2 100%) !important; }
.bg-gradient-green { background: linear-gradient(180deg, #6CCE25 0%, #B4EB45 100%) !important; }
.bg-gradient-red { background: linear-gradient(180deg, #E9203E 0%, #FF316E 100%) !important; }
.bg-gradient-grey { background: linear-gradient(180deg, #E0E0E0 0%, #EBEBEB 100%) !important; }
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
    min-height: 44px;
    padding: 10px 0;
    box-sizing: border-box !important;
    border: 0;
}
select, input {
    border-bottom: 1px solid grey;
}
button, .toast {
    border-radius: 6px;
    padding: 10px 10px;
}
.toast {
    background-color: #E9203E;
    color: white;
    position: fixed;
    top: 20px;
    left: 50%;
    z-index: 1000;
    transition: all .3s ease-in-out;
    width: calc(100vw - 40px);
    box-sizing: border-box;
    border: 4px solid rgba(255,255,255,.5);
    background-clip: padding-box;
    transform: translate(-50%, -70px);
    text-align: center;
}
.toast.active {
    transform: translate(-50%, 0);
}
.dimm {
    opacity: .3;
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

.preload {
    position: fixed;
    opacity: 0;
    pointer-events: none;
    width: 1px;
    height: 1px;
}
[v-cloak] { display: none; }

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

.tips-1, .tips-2, .tips-3, .tips-4, .tips-5 {
    padding: 0 50px;
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
    background-color: rgba(0,0,0,.8);

    opacity: 0;
    pointer-events: none;
    transition: all .2s ease-in-out;
}
.modal-overlay.active {
    opacity: 1;
    pointer-events: initial;
}
.modal {
    width: calc(100vw - 40px);
    box-sizing: border-box;
    margin: 100px auto 0 auto;
    padding: 10px 20px;
    border-radius: 6px;
    border: 4px solid rgba(255,255,255,.5);
    background-clip: padding-box;
    background-color: white;
    position: relative;
}
.modal-overlay .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 10px;
}
.modal-row, .modal-footer {
    width: 100%;
    margin-top: 10px;
    text-align: center;
}
.modal-header {
    text-align: center;
    font-weight: bold;
    width: 80%;
    margin: 3px auto 0 auto;
}
.modal button + button {
    margin-left: 10px;
}
</style>

<script>

export default {
	name: "App",
	data() {
		return {
            version: 'v20200307',
            session: {},
            tips: {},
            apiurl: location.href.indexOf('localhost')>-1?'http://127.0.0.1//my/arenavault/api/':'https://coisox.toyyib.la/',
            isMobile: false,
            toast: {
                show: false,
                text: null,
            }
		}
	},
	mounted() {
        window['app'] = this
        window.addEventListener("resize", this.checkIsMobile, { passive: true })
		this.checkIsMobile()
        this.session = JSON.parse(localStorage.getItem("ARENA_VAULT_SESSION") || '{}')
        this.tips = JSON.parse(localStorage.getItem("ARENA_VAULT_TIPS") || '{}')
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
			localStorage.removeItem("ARENA_VAULT_TIPS")
			this.checkSession()
        },
        updateSession(session) {
            this.session = session
            localStorage.setItem('ARENA_VAULT_SESSION', JSON.stringify(this.session))
        },
        updateTips(tips) {
            this.tips[tips] = true
            localStorage.setItem('ARENA_VAULT_TIPS', JSON.stringify(this.tips))
        },
        showToast(text) {
            var self = this
            self.toast.text = text
            self.toast.show = true
            setTimeout(() => { self.toast.show = false }, 1500);
        },
        goto(path, dimm) {
            if(!dimm) {
                if(this.$route.path==path) location.reload()
                else this.$router.push(path)
            }
        }
	},
	watch: {
		$route(to, from) {
			this.checkSession()
        }
	}
}
</script>
<template>
	<div class="p-absolute page bg-gradient-green">

		<div class="navbar d-flex">
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center" @click="showModalCode('Scan')">
					<span class="text-small">Join</span>
					<div><i class="material-icons-outlined green v-middle">all_out</i> <span class="green v-middle">SCAN CODE</span></div>
				</div>
			</div>
			<div class="nav-col p-relative d-flex">
				<button class="btn-circle" @click="showModalArena()"><i class="material-icons-outlined v-middle">add</i></button>
			</div>
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center" @click="showModalCode('Type')">
					<span class="text-small">Join</span>
					<div><i class="material-icons-outlined green v-middle">create</i> <span class="green v-middle">TYPE CODE</span></div>
				</div>
			</div>
		</div>

		<div class="content">
			<div class="table-row" v-for="item in arenas" :key="item.ARENA_ID">
				<div class="table-col-7" @click="activateArena(item)">{{item.ARENA_NAME}}</div>
				<div class="table-col-3 text-right">
					<i v-if="item.ARENA_ID!='000000'" class="material-icons-outlined red" @click="showModalShare(item.ARENA_ID, item.ARENA_NAME)">share</i>
					<template v-if="item.IS_OWNER">
						<i class="material-icons-outlined red" @click="showModalArena(item.ARENA_ID, item.ARENA_NAME)">create</i>
					</template>
				</div>
			</div>
            <div class="white dimm text-center mt-20">{{version}}</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalArena.show}" @click.self="modalArena.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalArena.show=false">close</i>
				<div class="modal-header green">{{modalArena.header}}</div>
				<div class="modal-row">
					<span class="text-small">Arena Name</span>
					<input type="text" v-model="modalArena.ARENA_NAME">
				</div>
				<div class="modal-footer d-flex center">
					<button class="bg-gradient-green" @click="saveModalArena"><i class="material-icons-outlined v-middle white">check</i> <span class="v-middle white">SAVE</span></button>
					<button class="bg-gradient-red" @click="deleteArena" v-if="modalArena.ARENA_ID"><i class="material-icons-outlined v-middle white">delete</i> <span class="v-middle white">DELETE</span></button>
				</div>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalShare.show}" @click.self="modalShare.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalShare.show=false">close</i>
				<div class="modal-header green">{{modalShare.ARENA_NAME}}</div>
				<div class="modal-row">
					<qrcode :value="modalShare.ARENA_ID" :options="{width:200, margin:0}"></qrcode>
					<div class="mt-10">{{modalShare.ARENA_ID}}</div>
				</div>
				<div class="modal-footer d-flex center">
					<button class="bg-gradient-orange" @click="modalShare.show=false"><i class="material-icons-outlined v-middle white">close</i> <span class="v-middle white">CLOSE</span></button>
				</div>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalCode.show}" @click.self="modalCode.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalCode.show=false">close</i>
				<div class="modal-header green">ADD ARENA</div>
				<div class="modal-row">
					<span class="text-small">{{modalCode.type}} Code</span>
					<input v-if="modalCode.type=='Type'" type="text" v-model="modalCode.ARENA_ID">
					<qrcode-stream v-else-if="modalCode.show" @decode="onDecode"></qrcode-stream>
				</div>
				<div class="modal-footer d-flex center">
					<button v-if="modalCode.type=='Type'" class="bg-gradient-green" @click="saveModalCode"><i class="material-icons-outlined v-middle white">check</i> <span class="v-middle white">SAVE</span></button>
				</div>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalTipsArena.show}" @click.self="modalTipsArena.show=false">
			<i class="material-icons-outlined btn-close white" @click="modalTipsArena.show=false">close</i>
			<div class="p-absolute white tips-1 text-center"><img src="img/tips_click.svg"><br>After you add arena into your list (either through add, scan or type), you must click on it to activate.<br><br>You can come back here anytime to switch to different arena (if more than 1 arena listed).</div>
		</div>

	</div>
</template>

<style scoped>
	.material-icons-outlined + .material-icons-outlined {
		margin-left: 10px;
	}
	.tips-1 {
		margin-top: 135px;
	}
</style>

<script>
import { QrcodeStream } from 'vue-qrcode-reader'

export default {
	data() {
		return {
			arenas: [],
			modalArena: {
				show: false,
				header: '',
				ARENA_ID: '',
				ARENA_NAME: '',
			},
			modalShare: {
				show: false,
				ARENA_ID: '0',
				ARENA_NAME: '',
			},
			modalCode: {
				show: false,
				type: 'Type',
				ARENA_ID: '',
			},
			modalTipsArena: {
				show: false,
			}
		};
	},
	components: {
		QrcodeStream,
	},
	props: ['session', 'tips', 'apiurl', 'version'],
	methods: {
		activateArena(item) {
			var s = Object.assign({}, this.session)
			s.ARENA_ID = item.ARENA_ID
			s.ARENA_OWNER = item.IS_OWNER
			s.PLAYER_ID = item.PLAYER_ID
			this.$emit('updateSession', s)
			this.$router.push('/standing')
		},
		showModalArena(ARENA_ID, ARENA_NAME) {
			this.modalArena.header = (ARENA_ID?'EDIT':'ADD')+' ARENA'
			this.modalArena.ARENA_ID = ARENA_ID
			this.modalArena.ARENA_NAME = ARENA_NAME
			this.modalArena.show = true
		},
		saveModalArena() {
			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.modalArena.ARENA_ID||'')
			formData.append('ARENA_NAME', self.modalArena.ARENA_NAME)
			formData.append('USER_ID', self.session.USER_ID)
			formData.append('type', self.modalArena.ARENA_ID?'edit':'new')

			fetch(self.apiurl+'arenaGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.arenas = response.arenas
					self.modalArena.show = false
				}
				else {
					console.log('Fail to update arena')
				}
			})
		},
		deleteArena() {
			var self = this
			var formData = new FormData()
			formData.append('USER_ID', self.session.USER_ID)
			formData.append('ARENA_ID', self.modalArena.ARENA_ID)
			formData.append('type', 'delete')

			fetch(self.apiurl+'arenaGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.arenas = response.arenas
					self.modalArena.show = false
				}
				else {
					console.log('Fail to delete arena')
				}
			})
		},
		saveModalCode() {
			var self = this
			var formData = new FormData()
			formData.append('USER_ID', self.session.USER_ID)
			formData.append('ARENA_ID', self.modalCode.ARENA_ID)
			formData.append('type', 'code')

			fetch(self.apiurl+'arenaGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.arenas = response.arenas
					self.modalCode.show = false
				}
				else {
					console.log('Fail to get arena')
				}
			})
		},
		onDecode(decodedString) {
			this.modalCode.ARENA_ID = decodedString
			this.saveModalCode()
		},
		getArenas() {
			var self = this
			var formData = new FormData()
			formData.append('USER_ID', self.session.USER_ID)

			fetch(self.apiurl+'arenaGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.arenas = response.arenas
				}
				else {
					console.log('Fail to get arena')
				}
			})
		},
		showModalShare(ARENA_ID, ARENA_NAME) {
			this.modalShare.ARENA_ID = ARENA_ID
			this.modalShare.ARENA_NAME = ARENA_NAME
			this.modalShare.show = true
		},
		showModalCode(type) {
			this.modalCode.show = true
			this.modalCode.type = type
		}
	},
	mounted() {
	   this.getArenas()
	   
		if(!this.tips.arena) {
			this.$emit('updateTips', 'arena')
			this.modalTipsArena.show = true
		}
	}
};
</script>
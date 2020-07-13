<template>
	<div class="p-absolute page bg-gradient-green">

		<div class="navbar d-flex">
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center px-10 w-100">
					<span class="text-small">Filter Player</span>
					<div>
						<select v-model="filter.player" class="outline-purple purple">
							<option value="">All</option>
							<option value="ghost">Ghost</option>
                            <option value="registered">Registered</option>
						</select>
					</div>
				</div>
			</div>
			<div class="nav-col p-relative d-flex">
				<button class="btn-circle" @click="showModalPlayer({})"><i class="material-icons-outlined v-middle">add</i></button>
			</div>
			<div class="nav-col p-relative d-flex center">
				
			</div>
		</div>

		<div class="content">
			<div class="table-row" v-for="item in filteredPlayers" :key="item.PLAYER_ID" @click="showModalPlayer(item)">
				<div class="table-col-max">{{item.USER_NAME}}</div>
                <div class="table-col-4 text-right d-flex flex-end">
                    <img v-if="item.USER_GMAIL.indexOf('ghost.')==0" src="img/icon_ghost.svg" height="24">
					<!-- <i v-if="item.USER_GMAIL.indexOf('ghost.')==0" class="material-icons-outlined red">no_sim</i> -->
				</div>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalPlayer.show}" @click.self="modalPlayer.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalPlayer.show=false">close</i>
				<div class="modal-header green">{{modalPlayer.header}}</div>
				<div class="modal-row">
					<span class="text-small">Player Name</span>
					<input type="text" v-model="modalPlayer.USER_NAME">
				</div>
				<div class="modal-row" v-if="modalPlayer.PLAYER_ID && modalPlayer.USER_GMAIL.indexOf('ghost.')==0">
					<span class="text-small">Bind Profile</span>
					<select v-model="modalPlayer.BIND_PLAYER_ID" class="outline-purple purple">
                        <option value="">Don't Bind</option>
                        <template v-for="item in players">
                            <option :key="item.PLAYER_ID" v-if="item.USER_NAME!=modalPlayer.USER_NAME && item.USER_GMAIL.indexOf('ghost.')<0" :value="item.PLAYER_ID">{{item.USER_NAME}} {{item.USER_GMAIL}}</option>
                        </template>
                    </select>
				</div>
				<div class="modal-footer d-flex center">
					<button class="bg-gradient-green" @click="saveModalPlayer"><i class="material-icons-outlined v-middle white">check</i> <span class="v-middle white">SAVE</span></button>
					<button class="bg-gradient-red" @click="deletePlayer" v-if="modalPlayer.PLAYER_ID"><i class="material-icons-outlined v-middle white">delete</i> <span class="v-middle white">DELETE</span></button>
				</div>
			</div>
		</div>

	</div>
</template>

<style scoped>
	.material-icons-outlined + .material-icons-outlined {
		margin-left: 10px;
	}
</style>

<script>

export default {
	data() {
		return {
            players: [],
			modalPlayer: {
				show: false,
				header: '',
				PLAYER_ID: '',
                USER_NAME: '',
                USER_ID: '',
                BIND_PLAYER_ID: ''
			},
            filter: {
                player: 'ghost'
            }
		};
	},
	props: ['session', 'tips', 'apiurl', 'version'],
	methods: {
		showModalPlayer(player) {
			this.modalPlayer.header = (player.PLAYER_ID?'EDIT':'ADD')+' PLAYER'
			this.modalPlayer.PLAYER_ID = player.PLAYER_ID
            this.modalPlayer.USER_NAME = player.USER_NAME
            this.modalPlayer.USER_ID = player.USER_ID
            this.modalPlayer.USER_GMAIL = player.USER_GMAIL
            this.modalPlayer.BIND_PLAYER_ID = ''
			this.modalPlayer.show = true
		},
		saveModalPlayer() {
			var self = this
			var formData = new FormData()
			formData.append('PLAYER_ID', self.modalPlayer.PLAYER_ID||'')
			formData.append('USER_NAME', self.modalPlayer.USER_NAME)
			formData.append('USER_ID', self.modalPlayer.USER_ID)
			formData.append('BIND_PLAYER_ID', self.modalPlayer.BIND_PLAYER_ID)
			formData.append('ARENA_ID', self.session.ARENA_ID)
			formData.append('type', self.modalPlayer.PLAYER_ID?'edit':'new')

			fetch(self.apiurl+'playerGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
                    self.players = response.players
                    self.modalPlayer.show = false
				}
				else {
					console.log('Fail to update player')
				}
			})
		},
		deletePlayer() {
			var self = this
			var formData = new FormData()
			formData.append('USER_ID', self.modalPlayer.USER_ID)
			formData.append('ARENA_ID', self.session.ARENA_ID)
			formData.append('type', 'delete')

			fetch(self.apiurl+'playerGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.players = response.players
                    self.modalPlayer.show = false
				}
				else {
					console.log('Fail to delete player')
				}
			})
		},
		getPlayers() {
			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)

			fetch(self.apiurl+'playerGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.players = response.players
				}
				else {
					console.log('Fail to get players')
				}
			})
		}
    },
    computed: {
        filteredPlayers() {
            var results = []
            var self = this
            self.players.forEach(function(item, i) {
                if(
                    self.filter.player=='' ||
                    (self.filter.player=='ghost' && item.USER_GMAIL.indexOf('ghost.')==0) ||
                    (self.filter.player=='registered' && item.USER_GMAIL.indexOf('ghost.')<0)
                ) results.push(item)
            })
			return results
		},
    },
	mounted() {
	   this.getPlayers()
	}
};
</script>
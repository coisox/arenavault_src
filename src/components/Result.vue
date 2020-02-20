<template>
	<div class="p-absolute page bg-gradient-purple">

		<div class="navbar d-flex">
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center px-10">
					<span class="text-small">Filter</span>
					<div>
						<select v-model="filter.player1" class="outline-purple purple">
							<option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
						</select>
					</div>
				</div>
			</div>
			<div class="nav-col p-relative d-flex">
				<button class="btn-circle bg-gradient-grey" @click="showModalResult({})"><i class="material-icons-outlined v-middle">add</i></button>
			</div>
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center px-10">
					<span class="text-small">Filter</span>
					<div>
						<select v-model="filter.player2" class="outline-purple purple">
							<option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="content">
			<template v-for="item in results">
				<div v-if="filterResults(item)" class="table-row" @click="showModalResult(item)" :key="item.RESULT_ID">
					<div class="table-col-7 truncate">
						<span class="text-small">{{item.CREATEDDATE}}</span>
						<div class="truncate">{{item.RESULT_P1_PLAYER_NAME}}</div>
						<div class="truncate">{{item.RESULT_P2_PLAYER_NAME}}</div>
					</div>
					<div class="table-col-1 text-center">
						<span class="text-small">S1</span>
						<div>{{item.RESULT_P1_SET1}}</div>
						<div>{{item.RESULT_P2_SET1}}</div>
					</div>
					<div class="table-col-1 text-center">
						<span class="text-small">S2</span>
						<div>{{item.RESULT_P1_SET2}}</div>
						<div>{{item.RESULT_P2_SET2}}</div>
					</div>
					<div class="table-col-1 text-center">
						<span class="text-small">S3</span>
						<div>{{item.RESULT_P1_SET3 || '-'}}</div>
						<div>{{item.RESULT_P2_SET3 || '-'}}</div>
					</div>
				</div>
			</template>
		</div>

		<div class="modal-overlay" id="modalResult" :class="{'active':modalResult.show}" @click.self="modalResult.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalResult.show=false">close</i>
				<div class="modal-header purple">EDIT RESULT</div>
				<div class="modal-row d-flex">
					<div class="table-col-max text-left">
						<span class="text-small">{{modalResult.CREATEDDATE}}</span>
						<div class="mt-10">
							<select v-model="modalResult.RESULT_P1_PLAYER_ID" class="outline-purple purple">
								<option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
							</select>
						</div>
						<div class="mt-10">
							<select v-model="modalResult.RESULT_P2_PLAYER_ID" class="outline-purple purple">
								<option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
							</select>
						</div>
					</div>
					<div class="table-col-1 text-center">
						<span class="text-small">S1</span>
						<div class="mt-10"><input type="text" class="" v-model="modalResult.RESULT_P1_SET1"></div>
						<div class="mt-10"><input type="text" v-model="modalResult.RESULT_P2_SET1"></div>
					</div>
					<div class="table-col-1 text-center">
						<span class="text-small">S2</span>
						<div class="mt-10"><input type="text" v-model="modalResult.RESULT_P1_SET2"></div>
						<div class="mt-10"><input type="text" v-model="modalResult.RESULT_P2_SET2"></div>
					</div>
					<div class="table-col-1 text-center">
						<span class="text-small">S3</span>
						<div class="mt-10"><input type="text" v-model="modalResult.RESULT_P1_SET3"></div>
						<div class="mt-10"><input type="text" v-model="modalResult.RESULT_P2_SET3"></div>
					</div>
				</div>
				<div class="modal-row d-flex center">
					<button class="bg-gradient-purple" @click="saveModalResult"><i class="material-icons-outlined v-middle white">check</i> <span class="v-middle white">SAVE</span></button>
					<button class="bg-gradient-red" @click="deleteResult" v-if="modalResult.RESULT_ID"><i class="material-icons-outlined v-middle white">delete</i> <span class="v-middle white">DELETE</span></button>
				</div>
			</div>
		</div>
		
	</div>
</template>

<style scoped>
	#modalResult .table-col-1 {
		margin-left: 5px;
	}
	#modalResult input {
		text-align: center;
		border: 1px solid grey;
		border-radius: 6px;
	}
</style>

<script>
export default {
	data() {
		return {
			filter: {
				player1: '',
				player2: ''
			},
			players: [],
			results: [],
			modalResult: {
				show: false,
				CREATEDDATE: '',
				RESULT_ID: '',
				RESULT_P1_PLAYER_ID: '',
				RESULT_P2_PLAYER_ID: '',
				RESULT_P1_SET1: '',
				RESULT_P1_SET2: '',
				RESULT_P1_SET3: '',
				RESULT_P2_SET1: '',
				RESULT_P2_SET2: '',
				RESULT_P2_SET3: '',
			}
		};
	},
	props: ['session', 'apiurl'],
	methods: {
		getResults() {
			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)

			fetch(self.apiurl+'resultGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.players = response.players
					self.results = response.results

					self.players.unshift({PLAYER_ID:'', USER_NAME:''})
					// for(var i=0; i<self.players.length; i++) self.players[i]['USER_NAME'] = self.ellipsis(self.players[i]['USER_NAME'], 14)
				}
				else {
					console.log('Fail to get results')
				}
			})
		},
		saveModalResult() {
			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)
			formData.append('RESULT_ID', self.modalResult.RESULT_ID||'')
			formData.append('RESULT_P1_PLAYER_ID', self.modalResult.RESULT_P1_PLAYER_ID)
			formData.append('RESULT_P2_PLAYER_ID', self.modalResult.RESULT_P2_PLAYER_ID)
			formData.append('RESULT_P1_SET1', self.modalResult.RESULT_P1_SET1)
			formData.append('RESULT_P1_SET2', self.modalResult.RESULT_P1_SET2)
			if(self.modalResult.RESULT_P1_SET3) formData.append('RESULT_P1_SET3', self.modalResult.RESULT_P1_SET3)
			formData.append('RESULT_P2_SET1', self.modalResult.RESULT_P2_SET1)
			formData.append('RESULT_P2_SET2', self.modalResult.RESULT_P2_SET2)
			if(self.modalResult.RESULT_P2_SET3) formData.append('RESULT_P2_SET3', self.modalResult.RESULT_P2_SET3)
			formData.append('type', self.modalResult.RESULT_ID?'edit':'new')

			fetch(self.apiurl+'resultGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.results = response.results
					self.modalResult.show = false
				}
				else {
					console.log('Fail to update result')
				}
			})
		},
		deleteResult() {
			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)
			formData.append('USER_ID', self.session.USER_ID)
			formData.append('RESULT_ID', self.modalResult.RESULT_ID)
			formData.append('type', 'delete')

			fetch(self.apiurl+'resultGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.results = response.results
					self.modalResult.show = false
				}
				else {
					console.log('Fail to delete result')
				}
			})
		},
		ellipsis(text, cap) {
			var t = text.substring(0, cap)
			if(text.length>cap) t += '...'
			return t
		},
		filterResults(item) {
			var searchString = item.RESULT_P1_PLAYER_ID + '_' + item.RESULT_P2_PLAYER_ID
			if(searchString.indexOf(this.filter.player1)>-1 && searchString.indexOf(this.filter.player2)>-1) return true
			return false
		},
		showModalResult(item) {
			this.modalResult.CREATEDDATE = item.CREATEDDATE
			this.modalResult.RESULT_ID = item.RESULT_ID
			this.modalResult.RESULT_P1_PLAYER_ID = item.RESULT_P1_PLAYER_ID
			this.modalResult.RESULT_P2_PLAYER_ID = item.RESULT_P2_PLAYER_ID
			this.modalResult.RESULT_P1_SET1 = item.RESULT_P1_SET1
			this.modalResult.RESULT_P1_SET2 = item.RESULT_P1_SET2
			this.modalResult.RESULT_P1_SET3 = item.RESULT_P1_SET3
			this.modalResult.RESULT_P2_SET1 = item.RESULT_P2_SET1
			this.modalResult.RESULT_P2_SET2 = item.RESULT_P2_SET2
			this.modalResult.RESULT_P2_SET3 = item.RESULT_P2_SET3
			this.modalResult.show = true
		},
	},
	mounted() {
		this.getResults()
	}
};
</script>
<template>
	<div class="p-absolute page bg-gradient-purple">

		<div class="navbar d-flex">
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center px-10 w-100">
					<span class="text-small">Filter Player</span>
					<div>
						<select v-model="filter.player" class="outline-purple purple">
							<option value="">All</option>
							<option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
						</select>
					</div>
				</div>
			</div>
			<div class="nav-col p-relative d-flex">
				<button class="btn-circle" @click="showModalResult({})"><i class="material-icons-outlined v-middle">add</i></button>
			</div>
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center px-10 w-100">
					<span class="text-small">Filter Status</span>
					<div>
						<select v-model="filter.status" class="outline-purple purple">
                            <option value="">All</option>
                            <option value="completed">Completed</option>
                            <option value="incomplete">Incomplete</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="content">
			<template v-for="(item, index) in filteredResults">
                <div v-if="index==0 || item.CREATEDDATE!=filteredResults[index-1].CREATEDDATE" class="table-header d-flex center" :key="'date'+item.RESULT_ID">{{item.CREATEDDATE}}</div>
				<div class="table-row" @click="showModalResult(item)" :key="item.RESULT_ID">
					<div class="table-col-4 truncate text-left">
						<div class="truncate">{{item.RESULT_P1_PLAYER_NAME}}</div>
					</div>
                    <div class="table-col-2 text-center">
						{{item.RESULT_OVERALL}}
					</div>
                    <div class="table-col-4 truncate text-right">
						<div class="truncate">{{item.RESULT_P2_PLAYER_NAME}}</div>
					</div>
				</div>
			</template>
		</div>

		<div class="modal-overlay" id="modalResult" :class="{'active':modalResult.show}" @click.self="modalResult.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalResult.show=false">close</i>
				<div class="modal-header purple">EDIT RESULT</div>
				<div class="modal-row d-flex">
					<div class="table-col-3 text-left pt-10 p-relative">Winner<i class="material-icons-outlined btn-swap purple" @click="swap">swap_horiz</i></div>
					<div class="table-col-max">
                        <select v-model="modalResult.RESULT_P1_PLAYER_ID" class="outline-purple purple">
                            <option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
                        </select>
					</div>
                </div>
                <div class="modal-row d-flex">
					<div class="table-col-3 text-left pt-10">Loser</div>
					<div class="table-col-max">
                        <select v-model="modalResult.RESULT_P2_PLAYER_ID" class="outline-purple purple">
                            <option v-for="item in players" :key="item.PLAYER_ID" :value="item.PLAYER_ID">{{item.USER_NAME}}</option>
                        </select>
					</div>
                </div>
                <div class="modal-row d-flex">
                    <div class="table-col-3 text-left pt-10">Result</div>
                    <div class="table-col-max d-flex">
                        <button :class="{'bg-gradient-purple white': modalResult.RESULT_OVERALL=='2-0', 'bg-gradient-grey': modalResult.RESULT_OVERALL!='2-0'}" @click="modalResult.RESULT_OVERALL='2-0'">2-0</button>
                        <button :class="{'bg-gradient-purple white': modalResult.RESULT_OVERALL=='2-1', 'bg-gradient-grey': modalResult.RESULT_OVERALL!='2-1'}" @click="modalResult.RESULT_OVERALL='2-1'">2-1</button>
                    </div>
                </div>
                <div class="divider mt-10"></div>
				<div class="modal-footer d-flex center">
					<button class="bg-gradient-purple" @click="saveModalResult"><i class="material-icons-outlined v-middle white">check</i> <span class="v-middle white">SAVE</span></button>
					<button class="bg-gradient-red" @click="deleteResult" v-if="modalResult.RESULT_ID"><i class="material-icons-outlined v-middle white">delete</i> <span class="v-middle white">DELETE</span></button>
				</div>
			</div>
		</div>
		
	</div>
</template>

<style scoped>
	#modalResult input {
		text-align: center;
		border: 1px solid grey;
		border-radius: 6px;
	}
    .table-header + .table-row,
    .table-row + .table-header
    {
        margin-top: 10px;
    }
    .btn-swap {
        position: absolute;
        top: 24px;
        left: 0;
        transform: rotate(90deg);
        padding: 10px;
    }
</style>

<script>
export default {
	data() {
		return {
			filter: {
				player: '',
				status: 'completed'
			},
			players: [],
			results: [],
			modalResult: {
				show: false,
				CREATEDDATE: '',
				RESULT_ID: '',
				RESULT_P1_PLAYER_ID: '',
				RESULT_P2_PLAYER_ID: '',
                RESULT_OVERALL: '',
            },
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
				}
				else {
					console.log('Fail to get results')
				}
			})
		},
		saveModalResult() {
            if(!this.modalResult.RESULT_P1_PLAYER_ID || !this.modalResult.RESULT_P2_PLAYER_ID || !this.modalResult.RESULT_OVERALL) {
                this.$emit('showToast', 'Please complete the details!')
                return
            }

			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)
			formData.append('RESULT_ID', self.modalResult.RESULT_ID||'')
			formData.append('RESULT_P1_PLAYER_ID', self.modalResult.RESULT_P1_PLAYER_ID)
			formData.append('RESULT_P2_PLAYER_ID', self.modalResult.RESULT_P2_PLAYER_ID)
			formData.append('RESULT_OVERALL', self.modalResult.RESULT_OVERALL)
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
		showModalResult(item) {
			this.modalResult.CREATEDDATE = item.CREATEDDATE
			this.modalResult.RESULT_ID = item.RESULT_ID
			this.modalResult.RESULT_P1_PLAYER_ID = item.RESULT_P1_PLAYER_ID
			this.modalResult.RESULT_P2_PLAYER_ID = item.RESULT_P2_PLAYER_ID
			this.modalResult.RESULT_OVERALL = item.RESULT_OVERALL
			this.modalResult.show = true
        },
        swap() {
            var temp = this.modalResult.RESULT_P1_PLAYER_ID
            this.modalResult.RESULT_P1_PLAYER_ID = this.modalResult.RESULT_P2_PLAYER_ID
            this.modalResult.RESULT_P2_PLAYER_ID = temp
        }
	},
    computed: {
        filteredResults() {
            var results = []
            var self = this
            self.results.forEach(function(item, i) {
                if(
                    (self.filter.player=='' || self.filter.player==item.RESULT_P1_PLAYER_ID || self.filter.player==item.RESULT_P2_PLAYER_ID) &&
                    (self.filter.status=='' || self.filter.status==item.RESULT_STATUS)
                ) results.push(item)
            })
			return results
		},
    },
	mounted() {
		this.getResults()
	}
};
</script>
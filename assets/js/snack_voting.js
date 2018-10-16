/***
	SNACK VOTING
	This is the main controller that handles the users input and
	interaction towards the voting REST API's.
	
	There are a total of four phases:
	
		Logging In - This is the initial log in form that enables 
			previous users that have already registered to log in.
			
		Registering - This enables users that don't have account to 
			create one. Very crudly yet secure enough.
		
		Voting - This phase allows the user to vote on the variety 
			of snacks provided by the cafeteria. Which are sorted 
			from most voted to least (DESC).
		
		Preview - Shows users that have logged in what they previously voted on. 
			While also being politly greeted.
*/
class SnackVoting extends React.Component {
	
	constructor(props) {
		super(props);
		
		// Set the important states
		this.state = {
			// Form Values
			studentID: "",
			password: "",
			fullname: "",
			snackVote: [],
			
			
			// Form Status
			formStatus: "Login",
			nextFormStatus: "Register",
			formErrorMessage: "",
			
			// Input Status
			inputStudentID: "",
			inputPassword: "",
			inputFullname: "",
			inputSubmit: "",
			submitHalt: false,
			
			// User Status
			userStatus: "notLoggedIn",
			userName: "",
			userVote: "",
			
			
			// Snack Array
			snackItems: [[]],
		};	
		
		// Seeing that this whole system is being contolled by this class
		// We can load all the snacks for future use.
		this.loadSnacks();
		
		
		// Bind all form input and submit handlers
		this.handleInputChange = this.handleInputChange.bind(this);
    		this.handleSubmit = this.handleSubmit.bind(this);
    		this.handleVote = this.handleVote.bind(this);
    		this.registerChange = this.registerChange.bind(this);
	}
	
	
	/**
		This funciton fetches the snack API to load in the snack data for voting
	*/
	loadSnacks() {
		fetch('assets/api/snacks/read.php')
		.then(res => res.json())
		.then((response) => {
			console.log('Loaded Snacks:', response);
			this.setState({			
				snackItems: response['results'],
			});
		})
		.catch(error => console.error('Error:', error));
	}
	
	
	/**
		After logging in this function finds out if the user has voted.
		This then determins what the user sees after logging on.
	*/
	checkUserStatus () {
		// Check the hasVoted REST API
		fetch('assets/api/votes/hasVoted.php', {
			method: "POST",
			mode: "cors",
			cache: "no-cache",
			credentials: "same-origin",
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			redirect: "follow",
			referrer: "no-referrer",
			body: JSON.stringify({
				// Load the student's ID
				studentID: this.state.studentID,
			}),
		}).then(res => res.json())
		.then((response) => {
		
			// Debug Message
			console.log('Succesful status check:', response);
			
			// Check the response message to see if they have voted
			// Then place them in their respective phase.
			// Note: the states that are being set are used in the users next phase
			if (response['message'] == "User has voted.") {
				this.setState({		
					userStatus: "voted",	
					userVote: response['vote'],
				});
			} else {
				this.setState({
					studentID: response['studentID'],
					userStatus: "notVoted",	
				});
			}
		})
		.catch(error => console.error('Error:', error));
		
		
	}
	
	
	/**
		Checks the users credential input to log them in.
		
		Note: This funcion is called "IS" Logged in, as further in development
		I would of created a cookie generator and checker for a keep me logged in function.
		This function would of checked if a cookie exists and log them in automaticly.
		
		Note: This functionality doesnt contain any session PHP data. Its purely to simulate
		a loggin function.
	*/
	isLoggedIn() {
		// < Implement cookie checker >
		
		// Use states to log in the student
		fetch('assets/api/students/login.php', {
			method: "POST",
			mode: "cors",
			cache: "no-cache",
			credentials: "same-origin",
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			redirect: "follow",
			referrer: "no-referrer",
			body: JSON.stringify({
				studentID: this.state.studentID,
				password : this.state.password,
			}),
		}).then(res => res.json())
		.then((response) => {
		
			// Debug Message
			console.log('Succesful Login:', response['message']);
			
			// Checks the message to see if the user has logged in.
			if (response['message'] == "Login Succesful.") {
				
				// The API returns their fullname for future use.
				this.setState({
					userName: response['fullname'],
				});
				
				// Check if the user has voted and place them into their respective phase.
				this.checkUserStatus();
			} else {
			
				// If they couldnt log in show the error message and stop the please waiting halt.
				this.setState({
					submitHalt: false,
					formErrorMessage: response['message'],
				});
			}
		})
		.catch(error => console.error('Error:', error));
	}
	
	
	
	/**
		This function handles the onChange events to set the states of their respective form inputs.
	*/
	handleInputChange(event) {
		const target = event.target;
		const value = target.value;
		const name = target.name;
		
		this.setState({
			[name]: value,
		});
	}
	
	
	
	/**
		Handle the submit of the first Login/Register form.
	*/
	handleSubmit(event) {
		// If the form is on login and the form is valid: Respectivly login the user.
		if (this.state.formStatus == "Login") {
			if (this.formValidation("Login")) {
				this.isLoggedIn();
			}
			
		// If the form is registering a student: register the student then log them in.
		} else {
			if (this.formValidation("Register")) {
			
				// Make sure you save the previous state and put a halt on registering any more students.
				var temp = this.state.formStatus;
				var hasRegistered = false;
				this.setState({
					formStatus: "Please Wait...",
					submitHalt: true,
				});
				
				// Create a student using the student form input states
				fetch('assets/api/students/create_user.php', {
					method: "POST",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					redirect: "follow",
					referrer: "no-referrer",
					body: JSON.stringify({
						studentID: this.state.studentID,
						fullname: this.state.fullname,
						password : this.state.password,
					}),
				}).then(res => res.json())
				.then((response) => {
				
					// Debug message
					console.log('Success:', response['message']);
					
					// If the user has'nt been created. Show the user why and enable them to sign in again
					if (response['message'] != "User was created.") {
						this.setState({
							formStatus: temp,
							submitHalt: false,
							formErrorMessage: response['message'],
						});
					} else {
						// Set if they have registered
						hasRegistered = true;	
					}
				})
				.catch((error) => console.error('Error:', error));
				
				// Log the student in if they have been registered after 1 second, just incase the request takes a little while
				setTimeout(function() {
					if (hasRegistered) {
						this.isLoggedIn();
					}
				}.bind(this), 1000);
			}
		}
		
		// Don't reset the webpage
		event.preventDefault();
	}
	
	
	
	/**
		Handle when a user votes
	*/
	handleVote(event) {
		fetch('assets/api/votes/castVote.php', {
			method: "POST",
			mode: "cors",
			cache: "no-cache",
			credentials: "same-origin",
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			redirect: "follow",
			referrer: "no-referrer",
			body: JSON.stringify({
				studentID: this.state.studentID,
				snackID: this.state.snackVote,
			}),
		}).then(res => res.json())
		.then((response) => {
			
			// Debug message
			console.log('Success:', response['message']);
			
			// If the users vote was accepted show them their voting screen
			if (response['message'] == "User vote was accepted.") {
				this.setState({		
					userStatus: "voted",	
					userVote: response['vote'],
				});
			}
		})
		.catch((error) => {console.error('Error:', error);});
		event.preventDefault();
	}
	
	
	
	/**
		Handle form changing states (Login to Register or visa versa).
	*/
	registerChange() {
		var temp = this.state.formStatus;
		this.setState({
			formStatus: this.state.nextFormStatus,
			nextFormStatus: temp 
		});
		event.preventDefault();
	}
	
	
	
	/**
		Validate the respective form given
		Note: I prefer all perams in functions to have an underscore towards the left of them
		unless im respective normal conventions. Like react constructors.
	*/
	formValidation(_status) {
	
		// Simple form validation that returns true when valid
		var isValid = true;
		if (_status == "Login") {
			if (this.state.studentID == "") {
				this.setState({
					inputStudentID	: "is-invalid",
				});
				isValid = false;
			}
			if (this.state.password == "") {
				this.setState({
					inputPassword	: "is-invalid",
				});
				isValid = false;
			}
		}
		return isValid;
	}
	
	
	
	/**
		Returns the HTML for all the snack choices
	*/
	snackRender() {
	
		// Create all the radios for each snack that was generated
		const content = this.state.snackItems.map((post) =>
			<div class="radio">
				<input type="radio" id={post['name']} 
				name="snackVote" value={post['id']}  onChange={this.handleInputChange}/>
				<label for={post['name']} >{post['name']} </label>
			</div>
		);
		
		// Return them in a neat box
		return (
		<div>
			<form onSubmit={this.handleVote}>
				{content}
				<br/>
				<input class="btn btn-primary" type="submit" value="Cast Vote" />
			</form>
		</div>
		);
	}
	
	
	
	/**
		Render all the required fields and information
	*/
	render() {
		return ( 
		<div>
			{this.state.userStatus == "notLoggedIn" ?
				<div class="form-group col-sm-12 loginForm">
					<form onSubmit={this.handleSubmit} id="mainform">
						<h2>{this.state.formStatus}</h2>
						<br/>
						
						{this.state.formErrorMessage != "" ?
							<div>
								<div class="text-danger">
									{this.state.formErrorMessage}
								</div>
								<br/>
							</div>
						: null}
						
						<label for="inputStudentID"> StudentID: </label>
						<input id="inputStudentID" class={"form-control col-sm-12 " + this.state.inputStudentID}
							name="studentID" type="text" value={this.state.studentID} onChange={this.handleInputChange} />
						<br/>
						
						{this.state.formStatus == "Register" ?
							<div>
								<label for="inputFullName">Full Name: </label>
								<input id="inputFullName" class="form-control col-sm-12" 
									name="fullname" type="text" value={this.state.fullname} onChange={this.handleInputChange} />
								<br/>
							</div>
						: 
							null
						}
						
						<label for="inputPassword"> Password: </label>
						<input id="inputPassword" class={"form-control col-sm-12 " + this.state.inputPassword}
							name="password" type="password" value={this.state.password} onChange={this.handleInputChange} />
						
						<small id="passwordHelp" class="form-text text-muted">
							This login system is used to simulate how a student would sign in to vote. Note: this does use BCRYPT.
						</small>
						<br/> 
						
	        				<input class="btn btn-primary" type="submit" value={this.state.formStatus} disabled={this.state.submitHalt} />        				
	        				<input class="btn btn-outline-primary" type="button" value={this.state.nextFormStatus} onClick={this.registerChange} />
					</form>
				</div>
			:
				<div>
					{this.state.userStatus == "notVoted" ?
						<div class="loginForm">
							<h5 class="col-12">
								Hey {this.state.userName}! The school cafeteria wishes to provide healthier snacking options for its students. Whats your favourite fruit?
							</h5>
							<div class="col-sm-6 offset-sm-3">
								{this.snackRender()}
							</div>
						</div>
						
					:
						<div class="loginForm">
							<h4 class="col-12">Hey {this.state.userName}! You voted for {this.state.userVote}.</h4>
						</div>
					}
				</div>
			}
		</div>
		);
	}
}




// Commense snack voting
ReactDOM.render(
  <SnackVoting />,
  document.getElementById('snack_voting_container')
);




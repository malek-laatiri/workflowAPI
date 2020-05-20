import React from 'react'
import axios from 'axios';

class Formproperty extends React.Component {
    constructor(props) {
        super(props);
        this.state = {value: ''};

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(event) {
        this.setState({value: event.target.value});
    }

    handleSubmit(event) {
        console.log('Le nom a été soumis : ' + this.state.value);
        fetch('http://127.0.0.1:8000/priority/priorityCreate', {
            method: 'POST',
            body: data,
        });
        event.preventDefault();
    }


    render() {
        return (
            <form onSubmit={this.handleSubmit}>
                <label>
                    Name :
                    <input type="text" value={this.state.value} onChange={this.handleChange} />
                </label>
                <input type="submit" value="send" />
            </form>
        );
    }
}
export default Formproperty

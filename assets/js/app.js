import React from 'react';
import ReactDOM from 'react-dom';
import Formproperty from "./components/Formproperty";
import Navbar from "./components/navbar";


class App extends React.Component {

    componentDidMount() {
        fetch(this.props.url)
            .then(res => res.json())
            .then((data) => {
                this.setState({ contacts: data })
            })
            .catch(console.log)
    }
    render() {
        return (
            <div>
                <Navbar/>
                <Formproperty></Formproperty>
            </div>
        )
    }
}

ReactDOM.render(<App />, document.getElementById('root'));

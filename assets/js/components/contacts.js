import React ,{ Component } from 'react'
class Contacts extends Component{
    const CountryList = ({countries = []}) => (
        <div>
            This is the country list: <br/>
            <ul>
                {countries.map((_id, country) => (
                    <li key={_id}>
                        <h3>{country}</h3>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export default Contacts;
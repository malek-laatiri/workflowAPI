import React, { Component } from "react";
import Link from "react-router-dom/modules/Link";
import Switch from "react-router-dom/es/Switch";
import Redirect from "react-router-dom/es/Redirect";
import Route from "react-router-dom/es/Route";
import { Button, Navbar } from 'react-bootstrap'

class NavBar extends Component {
    render() {
        return (
            <div>
                <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                    <Link className={"navbar-brand"} to={"/"}> Symfony React Project </Link>
                    <div className="collapse navbar-collapse" id="navbarText">
                        <ul className="navbar-nav mr-auto">
                            <li className="nav-item">
                                <Link className={"nav-link"} to={"/posts"}> Posts </Link>
                            </li>

                            <li className="nav-item">
                                <Link className={"nav-link"} to={"/users"}> Users </Link>
                            </li>
                        </ul>
                    </div>
                </nav>
                <Switch>
                    <Redirect exact from="/" to="/users" />
                    <Route path="/users" component={Users} />
                    <Route path="/posts" component={Posts} />
                </Switch>
            </div>
        );
    }
}

export default NavBar;
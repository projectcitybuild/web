import * as React from 'react';
import StripeCheckout from 'react-stripe-checkout';

interface InitialState {
    selectedOption: DonationAmountOption,
    selectedAmount: number,
    customAmount: number,
    stripeToken?: string,
    stripeKey: string,
    submitRoute: string,
};

enum DonationAmountOption {
    SetAmount,
    CustomAmount,
}

export default class Component extends React.Component<{}, InitialState> {
    state: InitialState = {
        selectedOption: DonationAmountOption.SetAmount,
        selectedAmount: 3000,
        customAmount: 0,
        stripeKey: "",
        submitRoute: "",
    };

    private customAmountInput: HTMLInputElement; 
    private form: HTMLFormElement;

    componentDidMount() {
        // grab Stripe key from meta content, because we
        // can't pass it via props from Blade
        const stripeKey = document.head.querySelector('[name=stripe-key]').getAttribute('content');
        const submitRoute = document.head.querySelector('[name=stripe-submit]').getAttribute('content');
        
        this.setState({ 
            stripeKey: stripeKey,
            submitRoute: submitRoute,
        });
    }

    useCustomAmount = () => {
        this.setState({
            selectedOption: DonationAmountOption.CustomAmount,
        }, () => {
            this.customAmountInput.focus();
        });
    }

    useSetAmount = (amount: number) => {
        this.setState({
            selectedOption: DonationAmountOption.SetAmount,
            selectedAmount: amount,
        });
    }

    handleCustomAmountChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const newValue = Number(event.target.value);
        if (!Number.isNaN(newValue)) {
            this.setState({
                customAmount: newValue, 
            });
        }
    }

    isSetAmount = (amount: Number): Boolean => {
        return this.state.selectedOption == DonationAmountOption.SetAmount &&
               this.state.selectedAmount == amount;
    }

    isCustomAmount = (): Boolean => {
        return this.state.selectedOption == DonationAmountOption.CustomAmount;
    }

    onStripeTokenReceived = (token: any) => {
        this.setState({ stripeToken: token.id }, () => {
            this.form.submit();
        });
    }

    render() {
        return (
            <div>
                <div className="button-table">
                    <div className="button-table__row">
                        <button className={this.isSetAmount(500) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(500)}>$5</button>
                        <button className={this.isSetAmount(1000) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(1000)}>$10</button>
                        <button className={this.isSetAmount(2000) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(2000)}>$20</button>
                    </div>
                    <div className="button-table__row">
                        <button className={this.isSetAmount(3000) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(3000)}>$30</button>
                        <button className={this.isCustomAmount() ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={this.useCustomAmount}>
                            <i className="fas fa-keyboard"></i> Custom
                        </button>
                    </div>
                </div>

                <input
                    ref={input => { this.customAmountInput = input }}
                    className="input-text" 
                    type="text" 
                    placeholder="3.00" 
                    disabled={this.state.selectedOption == DonationAmountOption.SetAmount} 
                    value={this.state.customAmount.toString()}
                    onChange={this.handleCustomAmountChange}
                    />

                <form action={this.state.submitRoute} method="POST" ref={form => { this.form = form; }}>
                    <input type="hidden" name="stripe_token" value={this.state.stripeToken} />

                    <StripeCheckout
                        name="Project City Build"
                        description="One-Time Donation"
                        image="https://forums.projectcitybuild.com/uploads/default/original/1X/847344a324d7dc0d5d908e5cad5f53a61372aded.png"
                        amount={this.state.selectedOption == DonationAmountOption.SetAmount ? this.state.selectedAmount : this.state.customAmount}
                        stripeKey={this.state.stripeKey}
                        locale="auto"
                        currency="usd"
                        token={this.onStripeTokenReceived}
                        >
                        <button className="button button--large button--fill button--primary" type="button">
                            <i className="fas fa-credit-card"></i> Donate via Card
                        </button>
                    </StripeCheckout>
                </form>
            </div>
        );
    }
}
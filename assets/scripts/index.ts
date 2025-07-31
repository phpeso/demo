import $ from 'jquery'; // fomantic
import { deleteRefresh, tryRefresh } from "./include/refresh";

const currencies: { [key: string]: string } = await (async () => {
    try {
        return await (await fetch('/service.php/currencies')).json();
    } catch (e) {
        // If we encounter a syntax error, it's likely that onrender instance has expired,
        // but the HTML was loaded from the cache. Try to reload with a query then
        if (e instanceof SyntaxError) {
            tryRefresh();
            const output = document.getElementById('output') as HTMLParagraphElement;
            output.innerHTML = 'Backend error. Try refreshing the page or try again later';
            output.style.color = 'red';
            throw new Error('Unable to load currencies -- trying to refresh');
        }
    }
})();

// if we successfully retrieved the currency list, remove the refresh param
deleteRefresh();

interface Value {
    name: string,
    value: string,
    selected?: boolean,
}

function mapValues(selected: string): Value[] {
    const values: Value[] = [];

    for (const [code, label] of Object.entries(currencies)) {
        let country = code.slice(0, 2).toLowerCase();
        if (country[0] === 'x') {
            country = 'un';
        }
        const value: Value = {
            name: `<i class="${country} flag"></i>${code} - ${label}`,
            value: code,
        };
        if (code === selected) {
            value.selected = true;
        }
        values.push(value);
    }

    return values;
}

// @ts-ignore
$('.currency-from').dropdown({
    values: mapValues('EUR'),
});
// @ts-ignore
$('.currency-to').dropdown({
    values: mapValues('USD'),
});

const form = document.getElementById('conversion-form') as HTMLFormElement;
const output = document.getElementById('output') as HTMLParagraphElement;

interface Converted {
    amount: string,
    tail: string,
}

form.addEventListener('submit', async event => {
    event.preventDefault();

    const data = new FormData(form);
    const converted: Converted = await (await fetch('/service.php/convert?' + new URLSearchParams(data as any).toString())).json();

    const originalValue =
        new Intl.NumberFormat("en-US", { style: "currency", currency: data.get('from') as string })
            .format(Number.parseFloat(data.get('amount') as string));
    const convertedValue =
        new Intl.NumberFormat("en-US", { style: "currency", currency: data.get('to') as string })
            .format(Number.parseFloat(converted.amount));

    const tail = convertedValue.includes('.') ? converted.tail : '.' + converted.tail;

    output.innerHTML = `${originalValue} = ${convertedValue}<span class="tail">${tail}</span>`;
});

form.requestSubmit(); // demo data

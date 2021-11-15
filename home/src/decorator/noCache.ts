export function noCache(target: any, propName: string, description: PropertyDescriptor) {
    const originMethod = description.value;
    
    description.value = async function(...args: any[]) {
        const req = args[0];
        const res = args[1];
        const next = args[2];

        res.set('Cache-Control', 'no-cache, no-store, must-revalidate');
        res.set('Pragma', 'no-cache');
        res.set('Expires', '0');

        return originMethod.apply(this, args);
    }
}
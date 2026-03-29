import { brandLogoSrc } from '@/brand/brandLogo';

type BrandMarkProps = {
    className?: string;
};

export default function BrandMark({ className }: BrandMarkProps) {
    return (
        <img
            src={brandLogoSrc}
            alt=""
            width={40}
            height={40}
            className={className ?? 'h-10 w-10 shrink-0 rounded-2xl object-contain'}
            aria-hidden
        />
    );
}


import React from "react";
import { Card, CardContent } from "@/components/ui/card";
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from "@/components/ui/carousel";
import { ContentItem } from "@/types/content";
import { useIsMobile } from "@/hooks/use-mobile";

interface ContentCarouselProps {
  items: ContentItem[];
}

const ContentCarousel: React.FC<ContentCarouselProps> = ({ items }) => {
  const isMobile = useIsMobile();

  if (items.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center p-8 text-center border rounded-lg bg-gray-50 min-h-[300px]">
        <h3 className="text-xl font-medium text-gray-700">No content yet</h3>
        <p className="text-gray-500 mt-2">
          Add your first piece of content using the form above.
        </p>
      </div>
    );
  }

  return (
    <Carousel
      opts={{
        align: "start",
        loop: true,
      }}
      className="w-full"
    >
      <CarouselContent>
        {items.map((item) => (
          <CarouselItem key={item.id} className={isMobile ? "basis-full" : "basis-1/2 md:basis-1/3"}>
            <div className="p-1">
              <Card className="overflow-hidden">
                <CardContent className="p-0 relative group">
                  <div className="aspect-video overflow-hidden">
                    <img
                      src={item.image}
                      alt={item.description}
                      className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                    />
                  </div>
                  <div className="p-4 bg-white">
                    <p className="line-clamp-2 text-sm mb-2">{item.description}</p>
                    <a
                      href={item.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-primary hover:underline text-sm font-medium"
                    >
                      Read Article &rarr;
                    </a>
                  </div>
                </CardContent>
              </Card>
            </div>
          </CarouselItem>
        ))}
      </CarouselContent>
      <CarouselPrevious className="hidden md:flex" />
      <CarouselNext className="hidden md:flex" />
    </Carousel>
  );
};

export default ContentCarousel;
